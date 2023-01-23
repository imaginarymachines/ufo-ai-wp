import React, { useMemo } from 'react';
import { Snackbar } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
export default function useLoadingStatus() {
	//state for error messages
	const [ error, setError ] = React.useState( '' );
	//state for loading
	const [ loading, setLoading ] = React.useState( false );
	const clearError = () => {
		setError( '' );
	};

	const hasError = useMemo( () => {
		return error.length > 0;
	}, [ error ] );

	const ErrorSnackBar = () => {
		if ( ! error ) {
			return null;
		}
		return (
			<Snackbar
				onDismiss={ clearError }
				onRemove={ clearError }
				actions={ [
					{
						label: __( 'Dismiss' ),
						onClick: clearError,
					},
				] }
			>
				{ error }
			</Snackbar>
		);
	};
	return {
		error,
		setError,
		hasError,
		loading,
		setLoading,
		ErrorSnackBar,
	};
}
