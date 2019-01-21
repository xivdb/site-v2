//
// Shopping Cart UI
//
class WardrobeAPIClass
{
	//
	// Get an item from the API
	//
	getItem(id, callback)
	{
		$.ajax({
			url: `${API_URL}/item/${id}`,
			cache: false,
			success: function(data) {
				callback(data);
			},
			error: function(data) {
				console.error(data);
			}
		});
	}
}

// Watch for events
var WardrobeAPI = new WardrobeAPIClass();
