import React from 'react';
import { dispatch, select } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import apiFetch from '@wordpress/api-fetch';

/**
 * Fetches prompt from API
 *
 * @param  data
 */
export const fetchPrompt = async ( data ) => {
	return apiFetch( {
		path: '/content-machine/v1/post',
		method: 'POST',
		data,
	} ).then( ( res ) => {
		return res;
	} );
};

/**
 * Hook That gets data from the post we need for prompt request
 *
 * @return {Object} - object with getData function
 */
export const usePostData = () => {
	const getData = () => {
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
		return data;
	};
	return { getData };
};

/**
 * Hook that handles the prompt request and inserts blocks
 *
 * @return {Object} - object with error, loading, and handler
 */
const usePromptRequest = () => {
	const { getData } = usePostData();
	//state for error messages
	const [ error, setError ] = React.useState( '' );
	//state for loading
	const [ loading, setLoading ] = React.useState( false );
	//state for number of blocks
	const [ length ] = React.useState( 1 );
	const handler = () => {
		const data = getData();
		fetchPrompt( data )
			.then( ( res ) => {
				setError( '' );
				setLoading( true );
				if ( ! res.texts || ! res.texts.length ) {
					setLoading( false );
					return;
				}

				//loop through array with forEach
				res.texts.forEach( ( text ) => {
					const block = createBlock( 'core/paragraph', {
						content: text,
					} );
					dispatch( 'core/block-editor' ).insertBlocks( block );
				} );
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
