import React from 'react';

import { dispatch, select } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import apiFetch from '@wordpress/api-fetch';

const prompt = async ( data ) => {
	return apiFetch( {
		path: '/content-machine/v1/post',
		method: 'POST',
		data,
	} ).then( ( res ) => {
		if ( ! res.texts || ! res.texts.length ) {
			return;
		}

		//loop through array with forEach
		res.texts.forEach( ( text ) => {
			const block = createBlock( 'core/paragraph', {
				content: text,
			} );
			dispatch( 'core/block-editor' ).insertBlocks( block );
		} );
	} );
};
const usePromptRequest = () => {
	//state for error messages
	const [ error, setError ] = React.useState( '' );
	//state for loading
	const [ loading, setLoading ] = React.useState( false );
	//state for number of blocks
	const [ length ] = React.useState( 1 );
	const handler = () => {
		const categories =
			select( 'core/editor' ).getEditedPostAttribute( 'categories' );
		const tags = select( 'core/editor' ).getEditedPostAttribute( 'tags' );
		const title = select( 'core/editor' ).getEditedPostAttribute( 'title' );
		const post = select( 'core/editor' ).getCurrentPost();
		const data = {
			categories,
			tags,
			title,
			post: post ? post.id : 0,
			length,
		};
		prompt( data )
			.then( ( r ) => {
				setError( '' );
				setLoading( true );
				// eslint-disable-next-line
			const block = createBlock( 'core/paragraph', { content: r } );
				dispatch( 'core/block-editor' ).insertBlocks( block );
				setLoading( false );
			} )
			.catch( ( e ) => {
				if ( e.message ) {
					setError( e.message );
				} else {
					setError( 'An error occured' );
				}
				setLoading( false );
			} );
	};

	return {
		error,
		loading,
		length,
		setLoading,
		setError,
		handler,
	};
};
export default usePromptRequest;
