import React from 'react';
import { PanelBody, PanelRow, Button } from '@wordpress/components';
import { PluginSidebar } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import domReady from '@wordpress/dom-ready';

import { Notice } from '@imaginary-machines/wp-admin-components';
const __ = ( str ) => str;

import usePromptRequest from './usePromptRequest';
import { LoadingSpinner } from './components/icons';
import ConnectionStatus from './components/ConnectionStatus';
import { useConnectionCheck } from './api/checkConnection';
import { dispatch } from '@wordpress/data';
import { DocsLinks } from './settings/links';
const SideBar = () => {
	const { error, loading, handler } = usePromptRequest();
	const { connected, isCheckingConnection } = useConnectionCheck();
	return (
		<PluginSidebar
			name="ufo-ai"
			title={ __( 'Upcycled Found Objects' ) }
			icon={ 'smiley' }
		>
			<PanelBody title="AI Content">
				{ connected ? (
					<PanelRow>
						<Button
							onClick={ handler }
							variant="primary"
							disabled={ loading }
						>
							{ loading ? (
								<>
									<span className="screen-reader-text">
										Loading
									</span>
									<LoadingSpinner />
								</>
							) : (
								<>Add Generated Text</>
							) }
						</Button>
					</PanelRow>
				) : null }
				<PanelRow>
					<p>
						You can also click the smiley icon in any paragraph
						block to add text to that paragraph.
					</p>
				</PanelRow>
				<PanelRow>
					<Button
						variant="primary"
						label="AI Blocks generate text and have extra controls"
						onClick={ () => {
							dispatch( 'core/block-editor' ).insertBlock(
								wp.blocks.createBlock( 'ufo-ai-wp/create-text' )
							);
						} }
					>
						Add AI Block
					</Button>
				</PanelRow>
				<PanelRow>
					<DocsLinks />
				</PanelRow>
			</PanelBody>

			<PanelBody title="Api Status">
				<PanelRow>
					Connected:{ ' ' }
					{ isCheckingConnection ? (
						<LoadingSpinner />
					) : (
						<ConnectionStatus connected={ connected } />
					) }
				</PanelRow>
			</PanelBody>

			{ error ? <Notice description={ error } type="error" /> : null }
		</PluginSidebar>
	);
};

domReady( () => {
	registerPlugin( 'ufo-ai', {
		render: SideBar,
	} );
} );
