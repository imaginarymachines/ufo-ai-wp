import React from 'react';

import {
	Form,
	FormTable,
	TrSubmitButton,
} from '@imaginary-machines/wp-admin-components';
import apiFetch from '@wordpress/api-fetch';
import { DocsLinks } from './links';
import ApiKeyField from '../components/ApiKeyField';
import checkConnection from '../api/checkConnection';
//Function for saving settings
const saveSettings = async ( values ) => {
	const r = await apiFetch( {
		path: '/ufo-ai/v1/settings',
		method: 'POST',
		data: values,
	} ).then( ( res ) => {
		return res;
	} );
	return { update: r };
};

const SettingsForm = () => {
	const [ isSaving, setIsSaving ] = React.useState( false );
	const [ hasSaved, setHasSaved ] = React.useState( false );
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
	const onSubmit = ( e ) => {
		e.preventDefault();
		setIsSaving( true );
		saveSettings( values ).then( ( { update } ) => {
			setValues( { ...values, update } );
			setHasSaved( true );
		} );
	};

	//Reset the isSaving state after 2 seconds
	React.useEffect( () => {
		if ( hasSaved ) {
			const timer = setTimeout( () => {
				setIsSaving( false );
			}, 2000 );
			return () => clearTimeout( timer );
		}
	}, [ hasSaved ] );

	//Check if connected
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
