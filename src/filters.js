import React from 'react';
import { addFilter } from '@wordpress/hooks';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarButton } from '@wordpress/components';
import { usePostData, fetchPrompt } from './usePromptRequest';
import domReady from '@wordpress/dom-ready';
import { dispatch, select } from '@wordpress/data';
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

/**
 * Add an insert button to the block toolbar
 *
 * @param {Object} BlockEdit - BlockEdit component
 */
const InsertText = ( BlockEdit ) => {
	const { getData } = usePostData();
	const handler = ( clientId ) => {
		const data = getData();
		data.what = 'sentences';
		fetchPrompt( data ).then( ( res ) => {
			//not error and has texts key of array
			if ( ! res.error && res.texts && res.texts.length ) {
				let content = res.texts[ 0 ];
				//Get block
				const block = select( CORE_NAMESPACE ).getBlock( clientId );
				if ( block.attributes.content.length > 0 ) {
					content = block.attributes.content + ' ' + content;
				}
				//set first text to block
				dispatch( CORE_NAMESPACE ).updateBlockAttributes( clientId, {
					content,
				} );
			}
		} );
	};
	return ( props ) => {
		if ( props.name !== 'core/paragraph' ) {
			return <BlockEdit { ...props } />;
		}

		return (
			<>
				<BlockControls>
					<ToolbarButton
						icon={ 'smiley' }
						label="Suggest Text"
						onClick={ () => handler( props.clientId ) }
					/>
				</BlockControls>
				<BlockEdit { ...props } />
			</>
		);
	};
};

domReady( () => {
	addFilter( 'editor.BlockEdit', NAMESPACE, InsertText );
} );
