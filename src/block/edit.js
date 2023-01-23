import { __ } from '@wordpress/i18n';
import React from 'react';

import { useBlockProps, BlockControls } from '@wordpress/block-editor';

import './editor.scss';

import RunOnce from '../components/RunOnce';
import { ToolbarGroup, ToolbarButton, Spinner } from '@wordpress/components';
import { select } from '@wordpress/data';
import { getAiText } from '../api/useAiText';
import useLoadingStatus from '../useLoadingStatus';
export default function Edit( props ) {
	const { loading, setLoading, setError, ErrorSnackBar } = useLoadingStatus();
	const content = props.attributes.content || '';
	const hasRan = props.attributes.hasRan;
	const setAttributes = props.setAttributes;
	const [ temperature, setTemperature ] = React.useState( 1 );
	const insertHandler = () => {
		setLoading( true );
		const { title, excerpt } = select( 'core/editor' ).getCurrentPost();
		const about = excerpt ? `about ${ excerpt }` : '';
		const start = `A paragraph for a blog post called`;
		const prompt = `${ start } ${ title } ${ about }`;
		//Must have prompt and at least 3 words after the start
		if ( ! prompt || prompt.length < start.length + 3 ) {
			setError( __( 'Not enough text to generate a prompt' ) );
			setLoading( false );
			return;
		}
		getAiText( prompt, temperature )
			.then( ( res ) => {
				setAttributes( { content: res[ 0 ], hasRan: true } );
				//Change temperature for next request
				const newTemp = Math.random() * 0.8 + 0.2;
				setTemperature( Math.round( newTemp * 100 ) / 100 );
				setLoading( false );
			} )
			.catch( ( err ) => {
				setError( err.message );
				setLoading( false );
			} );
	};

	return (
		<p { ...useBlockProps() }>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ 'redo' }
						label="Edit"
						onClick={ insertHandler }
					/>
				</ToolbarGroup>
			</BlockControls>
			{ loading ? <Spinner /> : null }
			{ ! hasRan ? <RunOnce fn={ insertHandler } /> : null }
			{ content }
			<ErrorSnackBar />
		</p>
	);
}
