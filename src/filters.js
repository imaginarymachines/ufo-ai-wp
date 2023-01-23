import React from 'react';
import { addFilter } from '@wordpress/hooks';
import { BlockControls } from '@wordpress/block-editor';
import domReady from '@wordpress/dom-ready';
import { dispatch, select } from '@wordpress/data';
import { Toolbar, ToolbarDropdownMenu } from '@wordpress/components';
import { LoadingSpinner } from './components/icons';
const __ = ( text ) => text;
/**
 * Namespace for all filters
 */
const NAMESPACE = 'ufo-ai';
/**
 * Namespace for core editor data
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/data/data-core-block-editor/
 *
 */
const CORE_NAMESPACE = 'core/block-editor';
import { getAiText } from './api/useAiText';
import useLoadingStatus from './useLoadingStatus';

const getTextBefore = ( clientId ) => {
	const blocks = select( 'core/block-editor' ).getBlocks();
	const index = blocks.findIndex( ( block ) => block.clientId === clientId );
	if ( index === 0 ) {
		return false;
	}
	const parts = [];
	//find the first block of the type before this block
	for ( let i = index; i >= 0; i-- ) {
		//Push the content of the block to the array
		//If is a heading or paragraph
		if (
			[ 'core/heading', 'core/paragraph' ].includes( blocks[ i ].name )
		) {
			parts.push( blocks[ i ].attributes.content );
		}
		if ( parts.length > 2 ) {
			break;
		}
	}
	//return parts in reverse order
	return parts.reverse().join( '\n' );
};

const WrappedBlockEdit = ( props ) => {
	const { BlockEdit, clientId } = props;

	const { setLoading, loading, setError, ErrorSnackBar, hasError } =
		useLoadingStatus();

	const insertHandler = () => {
		setLoading( true );
		let prompt = getTextBefore( clientId );
		if ( ! prompt ) {
			setError( __( 'Not enough text to generate a prompt' ) );
			setLoading( false );
			return;
		}
		if ( prompt.length < 50 ) {
			const title =
				select( 'core/editor' ).getEditedPostAttribute( 'title' );
			prompt = title + '\n' + prompt;
		}
		//Random temp to keep things spicy
		let temperature = Math.random() * 0.8 + 0.2;
		//round to 2
		temperature = Math.round( temperature * 100 ) / 100;
		getAiText( prompt, temperature )
			.then( ( res ) => {
				if ( ! res.error ) {
					let content = res[ 0 ];
					const block = select( CORE_NAMESPACE ).getBlock( clientId );

					//Get block
					if ( block.attributes.content.length > 0 ) {
						content = block.attributes.content + ' ' + content;
					}
					//set first text to block
					dispatch( CORE_NAMESPACE ).updateBlockAttributes(
						clientId,
						{
							content,
						}
					);
				}
				setLoading( false );
			} )
			.catch( ( error ) => {
				setError( error.message );
				setLoading( false );
			} );
	};

	//Remove controls when loading
	const controls = loading
		? []
		: [
				{
					title: 'Add More Text',
					icon: 'smiley',
					onClick: insertHandler,
				},
		  ];
	return (
		<>
			<BlockControls>
				<Toolbar label="Options">
					<ToolbarDropdownMenu
						icon={ loading ? <LoadingSpinner /> : 'smiley' }
						label="UFO AI"
						controls={ controls }
					/>
				</Toolbar>
			</BlockControls>
			<BlockEdit { ...props } />
			{ loading ? <LoadingSpinner /> : null }
			{ hasError ? <ErrorSnackBar /> : null }
		</>
	);
};

/**
 * Add menu  to the block toolbar
 *
 * @param {Object} BlockEdit - BlockEdit component
 */
const UfoMenu = ( BlockEdit ) => {
	return ( props ) => {
		if ( ! [ 'core/paragraph' ].includes( props.name ) ) {
			return <BlockEdit { ...props } />;
		}
		return <WrappedBlockEdit BlockEdit={ BlockEdit } { ...props } />;
	};
};

domReady( () => {
	addFilter( 'editor.BlockEdit', NAMESPACE, UfoMenu );
} );
