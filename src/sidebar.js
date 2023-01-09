import React from 'react';
import { PanelBody, PanelRow, Button } from '@wordpress/components';
import { PluginSidebar } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import domReady from '@wordpress/dom-ready';

import { Notice } from '@imaginary-machines/wp-admin-components';
const __ = ( str ) => str;

import usePromptRequest from './usePromptRequest';
import { LoadingSpinner } from './components/icons';
const SideBar = () => {
	const { error, loading, handler } = usePromptRequest();

	return (
		<PluginSidebar
			name="ufo-ai"
			title={ __( 'Upcycled Found Objects' ) }
			icon={ 'smiley' }
		>
			<PanelBody title="Add Content">
				<PanelRow>
					<Button onClick={ handler } variant="primary">
						Add Blocks
					</Button>
				</PanelRow>
			</PanelBody>

			{ loading ? <LoadingSpinner /> : null }
			{ error ? <Notice description={ error } type="error" /> : null }
		</PluginSidebar>
	);
};

domReady( () => {
	registerPlugin( 'ufo-ai', {
		render: SideBar,
	} );
} );
