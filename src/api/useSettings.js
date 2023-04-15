import React from 'react';
import apiFetch from '@wordpress/api-fetch';

//Function for saving settings
const saveSettings = async (values) => {
	const r = await apiFetch({
		path: '/ufo-ai/v1/settings',
		method: 'POST',
		data: values,
	}).then((res) => {
		return res;
	});
	return { update: r };
};

/**
 * Hook for saving settings
 *
 * @returns {Object} {saveSettings: function, isSaving: boolean, hasSaved: boolean}
 */
export const useSettings = () => {
	const [isSaving, setIsSaving] = React.useState(false);
	const [hasSaved, setHasSaved] = React.useState(false);

	//Reset the isSaving state after 2 seconds
	React.useEffect(() => {
		if (hasSaved) {
			const timer = setTimeout(() => {
				setIsSaving(false);
			}, 2000);
			return () => clearTimeout(timer);
		}
	}, [hasSaved]);
	return {
		saveSettings: (values) => {
			setIsSaving(true);
			saveSettings(values).then(() => {
				setIsSaving(false);
				setHasSaved(true);
			});
		},
		isSaving,
		hasSaved
	};
};
export default useSettings;
