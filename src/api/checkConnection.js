import apiFetch from '@wordpress/api-fetch';
const checkConnection = async () => {
	const is = await apiFetch( {
		path: '/ufo-ai/v1/connected',
		method: 'GET',
	} )
		.then( ( res ) => {
			if ( res ) {
				return res.connected;
			}
			return false;
		} )
		.catch( () => {
			return false;
		} );
	return is;
};

export const useConnectionCheck = () => {
	const [ connected, setConnected ] = React.useState( false );
	const [ checking, setChecking ] = React.useState( false );
	React.useEffect( () => {
		setChecking( true );
		checkConnection().then( ( is ) => {
			setConnected( is );

			setChecking( false );
		} );
	}, [] );
	return { connected, isCheckingConnection: checking };
};
export default checkConnection;
