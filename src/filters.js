import React from 'react';
import { addFilter } from '@wordpress/hooks';
import { BlockControls } from '@wordpress/block-editor';
import { usePostData, fetchPrompt } from './usePromptRequest';
import domReady from '@wordpress/dom-ready';
import { dispatch, select } from '@wordpress/data';
import { Toolbar, ToolbarDropdownMenu } from '@wordpress/components';
import { EditMark, LoadingSpinner } from './components/icons';
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

import apiFetch from '@wordpress/api-fetch';

/**
 * Fetches edit from API
 *
 * @param {Object} data - data to send to API
 */
export const fetchEdit = async ( data ) => {
	return apiFetch( {
		path: '/ufo-ai/v1/edit',
		method: 'POST',
		data,
	} ).then( ( res ) => {
		return res;
	} );
};

/**
 * Add menu  to the block toolbar
 *
 * @param {Object} BlockEdit - BlockEdit component
 */
const UfoMenu = ( BlockEdit ) => {
	const { getData } = usePostData();
	const insertHandler = ( clientId ) => {
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

	const editHandler = ( clientId, type ) => {
		const instruction = 'Fix spelling';
		switch ( type ) {
			default:
				break;
		}
		//get block
		const block = select( CORE_NAMESPACE ).getBlock( clientId );
		//if found, get conent
		if ( block ) {
			const input = block.attributes.content;
			//send to api
			fetchEdit( { input, instruction } ).then( ( res ) => {
				//if no error, update block
				if ( ! res.error && res.texts ) {
					dispatch( CORE_NAMESPACE ).updateBlockAttributes(
						clientId,
						{
							content: res.texts[ 0 ],
						}
					);
				}
			} );
		}
	};

	return ( props ) => {
		if ( ! [ 'core/paragraph' ].includes( props.name ) ) {
			return <BlockEdit { ...props } />;
		}
		const [ loading, setLoading ] = React.useState( false );

		//Remove controls when loading
		const controls = loading
			? []
			: [
					{
						title: 'Add More Text',
						icon: 'smiley',
						onClick: () => {
							setLoading( true );
							insertHandler( props.clientId );
							setLoading( false );
						},
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
			</>
		);
	};
};

domReady( () => {
	addFilter( 'editor.BlockEdit', NAMESPACE, UfoMenu );
} );
