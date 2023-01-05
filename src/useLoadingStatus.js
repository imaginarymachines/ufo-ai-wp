import React from 'react';

export default function useLoadingStatus() {
	//state for error messages
	const [ error, setError ] = React.useState( '' );
	//state for loading
	const [ loading, setLoading ] = React.useState( false );
	return {
		error,
		setError,
		loading,
		setLoading,
	};
}
