
import React from 'react';
import {PanelBody,Button  } from '@wordpress/components';
import { PluginSidebar } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { dispatch, select } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import apiFetch from '@wordpress/api-fetch';
const __ = ( str ) => str;
const prompt = async ( data ) => {
	apiFetch( {
		path: '/content-machine/v1/post',
		method: 'POST',
		data,
	} ).then( ( res ) => {
		console.log({res});
		if( !res.texts){
			return;
		}
		 //Create a paragrah block
		 const block = createBlock(
			'core/paragraph', { content: res.texts[0] }
		   );
		  //Insert that block
		  dispatch( 'core/block-editor' ).insertBlocks( block );
	} );
	return;
	apiFetch( { path: '/wp/v2/posts' } ).then( ( posts ) => {
		console.log( posts );
	} );
	return;
	return apiFetch({path:'/content-machine/v1/post',method:'POST',data}).then((r)=>{
		console.log({r});
		return r;
	});
};
const SideBar = () => {
	const handler = () => {
		const categories = select( 'core/editor' ).getEditedPostAttribute( 'categories' );
		const tags = select( 'core/editor' ).getEditedPostAttribute( 'tags' );
		const title = select( 'core/editor' ).getEditedPostAttribute( 'title' );
		const post = select( 'core/editor' ).getCurrentPost();
		const data = {
			categories,
			tags,
			title,
			post: post ? post.id : 0,
		};
		console.log({data});
		prompt( data ).then( ( r ) => {
			// eslint-disable-next-line
			const block = createBlock( 'core/paragraph', { content: r } );
			dispatch( 'core/block-editor' ).insertBlocks( block );
		} );
	};

	return (
		<PluginSidebar
			name="content-machine"
			title={ __( 'Content Machine' ) }
			icon={ 'smiley' }
		>
			<PanelBody>
				<Button
					onClick={ handler }
					isPrimary>
					Add Blocks
				</Button>
			</PanelBody>
		</PluginSidebar>
	);
};

domReady( () => {
	registerPlugin( 'content-machine', {
		render: SideBar,
	} );
});
