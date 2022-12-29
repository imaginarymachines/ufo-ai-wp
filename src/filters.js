import React from 'react';
import { addFilter } from '@wordpress/hooks';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarButton } from '@wordpress/components';
import { usePostData, fetchPrompt } from './usePromptRequest';
import domReady from '@wordpress/dom-ready';
import { dispatch } from '@wordpress/data';
/**
 * Namespace for all filters
 */
const NAMESPACE = 'content-machine';

/**
 * Add an insert button to the block toolbar
 *
 * @param {Object} BlockEdit - BlockEdit component
 */
const InsertText = ( BlockEdit ) => {
	const { getData } = usePostData();
	const handler = ( clientId ) => {
		const data = getData();
		fetchPrompt( data ).then( ( res ) => {
			//not error and has texts key of array
			if ( ! res.error && res.texts && res.texts.length ) {
				//set first text to block
				dispatch( 'core/block-editor' ).updateBlockAttributes(
					clientId,
					{ content: res.texts[ 0 ] }
				);
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
