import { __ } from '@wordpress/i18n';

import { useBlockProps } from '@wordpress/block-editor';

import './editor.scss';

import RunOnce from '../components/RunOnce';
import { LoadingSpinner } from '../components/icons';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { select } from '@wordpress/data';
import { getAiText } from '../api/useAiText';
export default function Edit( props ) {
	const loading = false;
	const content = props.attributes.content || '';
	const hasRan = props.attributes.hasRan;
	const setAttributes = props.setAttributes;
	const [ temperature, setTemperature ] = React.useState( 1 );
	const insertHanlder = () => {
		const { title, excerpt } = select( 'core/editor' ).getCurrentPost();
		const about = excerpt ? `about ${ excerpt }` : '';
		const prompt = `A paragraph for a blog post called ${ title } ${ about }`;
		getAiText( prompt, temperature ).then( ( res ) => {
			setAttributes( { content: res[ 0 ], hasRan: true } );
			//Change temperature for next request
			const newTemp = Math.random() * 0.8 + 0.2;
			setTemperature( Math.round( newTemp * 100 ) / 100 );
		} );
	};

	const refresHandler = () => {
		setAttributes( { content: '' } );
		insertHanlder();
	};

	return (
		<p { ...useBlockProps() }>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ 'redo' }
						label="Edit"
						onClick={ refresHandler }
					/>
				</ToolbarGroup>
			</BlockControls>
			{ loading ? <LoadingSpinner /> : null }
			{ ! hasRan ? <RunOnce fn={ insertHanlder } /> : null }
			{ content }
		</p>
	);
}
