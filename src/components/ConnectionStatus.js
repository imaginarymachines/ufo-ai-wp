import React from 'react';
import { ApiKeyLink } from '../settings/links';

import { CheckMark } from './icons';
export default function ConnectionStatus( { connected } ) {
	//if true, show checkmark, else show linlk
	return (
		<span
			style={ {
				maxWidth: '25px',
				maxHeight: '25px',
			} }
		>
			{ connected ? <CheckMark /> : <ApiKeyLink /> }
		</span>
	);
}
