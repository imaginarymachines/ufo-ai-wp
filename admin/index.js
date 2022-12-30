import React from 'react';
import { render } from 'react-dom';

import {
	Form,
	FormTable,
	TrInput,
	TrSubmitButton
  } from  "@imaginary-machines/wp-admin-components";
import apiFetch from '@wordpress/api-fetch';
import { ExternalLink } from '@wordpress/components';

const utm = '?utm_source=wp-plugin-settings&utm_campaign=ufo-ai-wp';
const DocsLinks = () => (
  <ExternalLink
  		target="__blank"
  		href={`https://upcycledfoundobjects.com/docs${utm}`}
	>Documentation</ExternalLink>
);
const ApiKeyLink = () => (
	  <ExternalLink

	  			  target="__blank"
				  href={`https://upcycledfoundobjects.com/api-key${utm}`}>
									  Get API Key
				  </ExternalLink>
);

//Function for saving settings
const  saveSettings = async (values) => {
	const r = await apiFetch( {
		path: '/ufo-ai/v1/settings',
		method: 'POST',
		data: values,
	} ).then( ( res ) => {
		return res;
	} );
	return {update:r};
}


const SettingsForm = () => {
	const [isSaving,setIsSaving] = React.useState(false);
	const [hasSaved,setHasSaved] = React.useState(false);
	const [values,setValues] = React.useState(() => {
		if( CONTENT_MACHINE.settings ){
			return CONTENT_MACHINE.settings;
		}
		return {
			key:'',
			url: '',
		};
	});
	const id = "settings-form";
	const onSubmit = (e) => {
		e.preventDefault();
		setIsSaving(true);
		saveSettings(values).then(({update}) => {
			setValues({...values,update});
			setHasSaved(true);
		});
	}

	//Reset the isSaving state after 2 seconds
	React.useEffect(() => {
		if( hasSaved ){
			const timer = setTimeout(() => {
				setIsSaving(false);
			}, 2000);
			return () => clearTimeout(timer);
		}

	  }, [hasSaved]);
	return (
		<Form id={id} onSubmit={onSubmit}>
		  <FormTable >
			  <>
				  <TrInput
					  label={'Api Key'}
					  id={'input'}
					  name={'key'}
					  value={values.key}
					  onChange={(value) => setValues({...values,key:value})}
				  />
				  <TrSubmitButton
					  id={'submit-button'}
					  name={'submit-button'}
					  value={'Save'}
				  />
				  <>{isSaving ? "Saving..." : ""}</>
			  </>
		  </FormTable>
	  </Form>
	)
  }
const App = () => {
	  return <SettingsForm />
}

render(<App />, document.getElementById('ufo-ai-settings'));
