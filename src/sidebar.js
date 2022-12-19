import React from 'react';
import {
	PanelBody,
	PanelRow,
	Button,
	Spinner,
	TextControl,
} from '@wordpress/components';
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
const SideBar = () => {
	//state for error messages
	const [ error, setError ] = React.useState( '' );
	//state for loading
	const [ loading, setLoading ] = React.useState( false );
	//state for number of blocks
	const [ length, setLength ] = React.useState( 1 );
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

	return (
		<PluginSidebar
			name="content-machine"
			title={ __( 'Content Machine' ) }
			icon={ 'smiley' }
		>
			<PanelBody title="Add Content">
				<PanelRow>
					<Button onClick={ handler } variant="primary">
						Add Blocks
					</Button>
				</PanelRow>
				<PanelRow>
					<TextControl
						onChange={ ( val ) => setLength( val ) }
						value={ length }
						label={ __( 'How Many?' ) }
						type="number"
						min="1"
						step="1"
						max="4"
					/>
				</PanelRow>
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
