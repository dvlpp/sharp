#Sharp

*A Laravel CMS for developers who hate CMS*

Sharp is a Laravel CMS package which intent to greatly simplify data management of a website *without* implicating a special data storage or organization, being as much as possible data-agnostic. 

Well, I think I can try to be clearer: in many of my web projects there's a lot of business data to manage, and my clients need some tool to handle those texts, images, lists, links and other stuff. I can either develop each time a dedicated admin panel (hmm... no), or integrate a full CMS, in which case I often have to adapt my data storage technology and database schema to the CMS tool (or sometimes worse, the tool manages itself my data structure). Sharp is an decent attempt to keep the CMS cool part (inputs, validation, auth, ...) without the crap (Sharp doesn't know about my database nor about the way I want to organize my code).

OK, here's how this is working:

1. Installation

	- Through composer, add `"dvlpp/sharp": "~1.0"` in your require section, and run `composer update`.
	- Next add `'Dvlpp\Sharp\SharpServiceProvider'` in your app.php providers section.
	- And finally, run those two commands:
		- `php artisan config:publish dvlpp/sharp` to publish the two necessary config file (see below)
		- `php artisan asset:publish dvlpp/sharp` to publish JS and CSS used by Sharp

OK, you're good to go. One final note: after an composer update, always re-run the asset publish command to be sure to have the current assets version.

1. [Concepts](docs/concepts.md)

2. [The config file(s)](docs/config.md)

1. [Repository and data management](docs/repository.md)

3. [Entities lists](docs/entities_list.md) (renderers, sorting, activation, reordering, pagination, search)

4. Entity forms
	1. Fields
	2. About file management
	3. Validation
	4. Update
	5. Entity state

5. Auth
	1. Global auth
	2. Rights management

6. Commands
