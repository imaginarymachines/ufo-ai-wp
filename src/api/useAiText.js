import apiFetch from '@wordpress/api-fetch';
export const getAiText = async ( prompt, temperature ) => {
	return apiFetch( {
		path: '/ufo-ai/v1/text',
		method: 'POST',
		data: { prompt, temperature },
	} );
};

export default function useApiText() {
	return { getAiText };
}
