import React from 'react';

import {
	Form,
	FormTable,
	TrSubmitButton,
} from '@imaginary-machines/wp-admin-components';

import { DocsLinks } from './links';
import ApiKeyField from '../components/ApiKeyField';
import checkConnection from '../api/checkConnection';
import useSettings from '../api/useSettings';


const SettingsForm = () => {
	const {isSaving,hasSaved, saveSettings} = useSettings();
	const [ connected, setConnected ] = React.useState( false );
	const [ values, setValues ] = React.useState( () => {
		// eslint-disable-next-line no-undef
		if ( CONTENT_MACHINE.settings ) {
			// eslint-disable-next-line no-undef
			return CONTENT_MACHINE.settings;
		}
		return {
			key: '',
			url: '',
		};
	} );
	const id = 'settings-form';

	//Save settings handler
	const onSubmit = ( e ) => {
		e.preventDefault();
		saveSettings( values ).then( ( { update } ) => {
			setValues( { ...values, update } );
		} );
	};

	//Check if connected, when has saved
	React.useEffect( () => {
		checkConnection().then( ( is ) => {
			setConnected( is );
		} );
	}, [ hasSaved ] );

	return (
		<div>
			<DocsLinks />
			<Form id={ id } onSubmit={ onSubmit }>
				<FormTable>
					<>
						<ApiKeyField
							connected={ connected }
							value={ values.key }
							onChange={ ( value ) =>
								setValues( { ...values, key: value } )
							}
							isSaving={ isSaving }
						/>
						<TrSubmitButton
							id={ 'submit-button' }
							name={ 'submit-button' }
							value={ 'Save' }
						/>
						<>{ isSaving ? 'Saving...' : '' }</>
					</>
				</FormTable>
			</Form>
		</div>
	);
};
export default SettingsForm;
