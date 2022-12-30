import { render } from 'react-dom';

import React from 'react';

import {
	Tabs,

} from '@imaginary-machines/wp-admin-components';

import SettingsForm from './settingsTab';
const tabs = [
	{
		children: <div>Tab One Content</div>,
		id: 'welcome',
		label: 'Welcome'
	},
	{
		children: (<SettingsForm />),
		id: 'settings',
		label: 'Settings'
	},
	{
		children: <div>Tab Three Content</div>,
		id: 'account',
		label: 'Account'
	}
];

const USE_TABS = false;
const App = () => {
	if( ! USE_TABS ) {
		return (
			<SettingsForm />
		);
	}
	return (
		<Tabs
			initialTab="settings"
			tabs={tabs}
		/>
	)
};

render( <App />, document.getElementById( 'ufo-ai-settings' ) );
