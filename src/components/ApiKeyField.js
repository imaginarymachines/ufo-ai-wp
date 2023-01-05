import React from 'react';

import { FieldTr, Input } from '@imaginary-machines/wp-admin-components';
import ConnectionStatus from '../components/ConnectionStatus';

const name = 'key';
const label = 'Api Key';
const id = 'api-key';
const ApiKeyField = ( { value, onChange, connected, isSaving } ) => {
	return (
		<FieldTr name={ name } label={ label } id={ id }>
			<Input
				label={ label }
				id={ id }
				name={ name }
				value={ value }
				onChange={ onChange }
			/>
			{ ! isSaving ? <ConnectionStatus connected={ connected } /> : null }
		</FieldTr>
	);
};
export default ApiKeyField;
