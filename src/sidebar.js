
import React from 'react';
import {PanelBody,Button  } from '@wordpress/components';
import { PluginSidebar } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { dispatch, select } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';

const __ = ( str ) => str;
const prompt = async ( data ) => {
	console.log({data	});
	// eslint-disable-next-line
	const url = CMWP.prompt;
	// eslint-disable-next-line
	const r = await fetch( url, {
		method: 'POST',
		body: JSON.stringify( data ),
		headers: {
			'Content-Type': 'application/json; charset=utf-8',
		},
		// eslint-disable-next-line
	} ).then( ( r ) => r.json() ).then( ( r ) => {
		// eslint-disable-next-line
		console.log( { r } );
		return r;
	} );
	return r;
};
const SideBar = () => {
	const handler = () => {
		const categories = select( 'core/editor' ).getEditedPostAttribute( 'categories' );
		const tags = select( 'core/editor' ).getEditedPostAttribute( 'tags' );
		const title = select( 'core/editor' ).getEditedPostAttribute( 'title' );
		const data = {
			categories,
			tags,
			title,
		};
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
