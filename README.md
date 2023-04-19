# Upcyled Found Objects WordPress Plugin

A WordPress plugin, that uses a large lanaguage model to help you write your posts, for your post, based on the post you’re working on.

- Learn more: https://upcycledfoundobjects.com/
- Documentation: https://upcycledfoundobjects.com/docs


## Hooks

### Actions

- `ufoaiwp`
	- Runs when plugin is initalized
		- `ImaginaryMachines\UfoAi\Plugin $plugin`


### Filters

- `ufoaiwp_get_setting`
	- Runs before returning setting value
		- `mixed $setting` setting value
		- `string $key` Setting  name
		- `array $settings` All
- `ufoai_api_version`
	- Change API version
		- `string $version` Current version
- `ufoai_client_url`
	- Change the URL for API request
		- `string $url URL` for request
		- `string $endpoint`
		- `Client $client` API client
- `ufoaiwp_request_args`
	- Change wp_remote_request API request arguments
		- `array $args` Request arguments
		- `string $url`
## Develop

- Clone
	- git@github.com:imaginarymachines/ufo-ai-wp.git
- Install
	- `npm i`
	- Installs with npm and composer
- Start environment
	- `npm env start`
- Test PHP
	- `npm run test:php`
- Format all
	- `npm run format`
- Build JS/CSS
	- `npm run build`
- Create zip
	- `npm run zip`
	- Installs with composer optimized
	- Builds CSS/JS/Blocks
	- Makes a new zip
