import { addFilter } from '@wordpress/hooks';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarButton } from '@wordpress/components';

/**
 * Namespace for all filters
 */
const NAMESPACE = 'content-machine';

const Download = ( BlockEdit ) => {
	return ( props ) => {
		if ( props.name !== 'core/paragraph' ) {
			return <BlockEdit { ...props } />;
		}

		return (
			<>
				<BlockControls>
					<ToolbarButton
						icon={ 'smiley' }
						label="Suggest Text"
						onClick={ () =>
							alert( JSON.stringify( props.attributes ) )
						}
					/>
				</BlockControls>
				<BlockEdit { ...props } />
			</>
		);
	};
};

addFilter( 'editor.BlockEdit', NAMESPACE, Download );
