//
// Account
//
class AppLanguagesClass
{
	constructor()
	{
		this.data = {
			custom: null,
			params: null,
		};
	}

	//
	// Set some translations
	//
	set(type, data)
	{
		this.data[type] = data;
	}

	//
	// Get a custom language
	//
	custom(id)
	{
		return this.data.custom[id];
	}

	//
	// Get parameters
	//
	params(id)
	{
		return this.data.params[id];
	}
}
var languages = new AppLanguagesClass();
