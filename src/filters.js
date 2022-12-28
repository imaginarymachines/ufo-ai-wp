import React from 'react';
import { addFilter } from '@wordpress/hooks';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarButton } from '@wordpress/components';
import { usePostData, fetchPrompt } from './usePromptRequest';
import domReady from '@wordpress/dom-ready';

/**
 * Namespace for all filters
 */
const NAMESPACE = 'content-machine';

/**
 * Add an insert button to the block toolbar
 *
 * @param  BlockEdit
 */
const InsertText = ( BlockEdit ) => {
	const { getData } = usePostData();
	const handler = () => {
		const data = getData();
		fetchPrompt( data ).then( ( res ) => {
			//@TODO Modfiy text of block?
			//Maybe use a modal to give them an option to choose or regenerate?
			console.log( res );
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
						onClick={ handler }
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
