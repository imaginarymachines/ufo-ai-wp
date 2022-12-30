import { ExternalLink } from '@wordpress/components';

const utm = '?utm_source=wp-plugin-settings&utm_campaign=ufo-ai-wp';
export const DocsLinks = () => (
	<ExternalLink
		target="__blank"
		href={ `https://upcycledfoundobjects.com/docs${ utm }` }
	>
		Documentation
	</ExternalLink>
);
export const ApiKeyLink = () => (
	<ExternalLink
		target="__blank"
		href={ `https://upcycledfoundobjects.com/user/api-tokens${ utm }` }
	>
		Get API Key
	</ExternalLink>
);
