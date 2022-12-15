import React from 'react';
import { PanelBody, Button, Spinner } from '@wordpress/components';
import { PluginSidebar } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { dispatch, select } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import apiFetch from '@wordpress/api-fetch';
import { Notice } from '@imaginary-machines/wp-admin-components';
const __ = ( str ) => str;
const prompt = async ( data ) => {
	return apiFetch( {
		path: '/content-machine/v1/post',
		method: 'POST',
		data,
	} ).then( ( res ) => {
		if ( ! res.texts ) {
			return;
		}
		//Create a paragrah block
		const block = createBlock( 'core/paragraph', {
			content: res.texts[ 0 ],
		} );
		//Insert that block
		dispatch( 'core/block-editor' ).insertBlocks( block );
	} );
};
const SideBar = () => {
	//state for error messages
	const [ error, setError ] = React.useState( '' );
	//state for loading
	const [ loading, setLoading ] = React.useState( false );
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

	return (
		<PluginSidebar
			name="content-machine"
			title={ __( 'Content Machine' ) }
			icon={ 'smiley' }
		>
			<PanelBody>
				<Button onClick={ handler } variant="primary">
					Add Blocks
				</Button>
			</PanelBody>
			{ loading ? <Spinner /> : null }
			{ error ? <Notice description={ error } type="error" /> : null }
		</PluginSidebar>
	);
};

domReady( () => {
	registerPlugin( 'content-machine', {
		render: SideBar,
	} );
} );
