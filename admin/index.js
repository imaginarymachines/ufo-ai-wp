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
	}
	return (
		<Form id={id} onSubmit={onSubmit}>
		  <FormTable >
			  <>
				  <TrInput
					  label={'Input Field'}
					  id={'input'}
					  name={'input'}
					  value={values.key}
					  onChange={(value) => setValues({...values,input:value})}
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
