#Sharp

*A Laravel CMS for developers who hate CMS*

Sharp is a Laravel CMS package which intent to greatly simplify data management of a website *without* implicating a special data storage or organization, being as much as possible data-agnostic. 

Well, I think I can try to be clearer: in many of my web projects there's a lot of business data to manage, and my clients need some tool to handle those texts, images, lists, links and other stuff. I can either develop each time a dedicated admin panel (hmm... no), or integrate a full CMS, in which case I often have to adapt my data storage technology and database schema to the CMS tool (or sometimes worse, the tool manages itself my data structure). Sharp is an decent attempt to keep the CMS cool part (inputs, validation, auth, ...) without the crap (Sharp doesn't know about my database nor about the way I want to organize my code).

OK, here's how this is working:

1. [Concepts](docs/concepts.md)

2. [The config file(s)](docs/config.md)

3. Entities lists
	1. Renderers
	2. Sorting
	3. Reordering
	4. Pagination
	5. Search

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
