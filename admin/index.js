import React from 'react';
import { render } from 'react-dom';

import {
	Form,
	FormTable,
	FormProps,
	TrInput,
	TrSelect,
	TrSubmitButton
  }
  from  "@imaginary-machines/wp-admin-components";
import apiFetch from '@wordpress/api-fetch';

//Function for saving settings
const saveSettings = (values) => {
	apiFetch( {
		path: '/content-machine/v1/settings',
		method: 'POST',
		data: values,
	} ).then( ( res ) => {
		console.log(res);
	} );
}
const SettingsForm = () => {

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
		console.log(values);
		saveSettings(values);
	}
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
				  <TrSelect
					  label={'Select Field'}
					  id={'select'}
					  name={'select'}
					  value={values.select}
					  options={[
						{

							label:'One',
							value:'one'
						},
						{
							label:'Two',
							value:'two'
						},
					  ]}
					  onChange={(value) => setValues({...values,select:value})}
				  />
				  <TrSubmitButton
					  id={'submit-button'}
					  name={'submit-button'}
					  value={'Save'}
				  />
			  </>
		  </FormTable>
	  </Form>
	)
  }
const App = () => {
	  return <SettingsForm />
}

render(<App />, document.getElementById('content-machine-settings'));
