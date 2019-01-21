//
// Load gearset
//
class WardrobeLoaderClass
{
	constructor()
	{
		this.reset();
	}

	//
	// Reset
	//
	reset()
	{
		this.itemsTotal = 0;
		this.itemsLoaded = 0;
		this.materiaTotal = 0;
		this.materiaLoaded = 0;
		this.hasMateria = false;
	}


	load(gear)
	{
		// reset
		this.reset();

		// set total items
		this.itemsTotal = Object.size(gear);

		// add items
		for(var slot in gear) {
			this.addItem(slot, gear[slot]);
		}
	}

	//
	// Has complete?
	//
	hasComplete()
	{
		if (this.hasLoaded) {
			Wardrobe.loadingGearset();
		}
	}

	//
	// Add an item
	//
	addItem(slot, data)
	{
		// add item
		WardrobeItems.add(data.item, (item) => {
			this.itemsLoaded++;

			// if materia, add that
			if (data.materia) {
				this.addMateria(slot, data.materia);
			}

			if (!this.hasMateria && this.itemsLoaded >= this.itemsTotal) {
				this.hasLoaded = true;
				this.hasComplete();
			}
		});
	}

	//
	// Add materia
	//
	addMateria(slot, materia)
	{
		if (materia.length < 1) {
			return;
		}

		// materia exists
		this.hasMateria = true;
		this.hasLoaded = false;
		this.materiaTotal += materia.length;

		// loop through materia
		for(var i in materia) {
			var id = materia[i];

			WardrobeAPI.getItem(id, (item) => {;
				WardrobeMateria.setActive(item).attach(slot);
				this.materiaLoaded++;

				if (this.materiaLoaded >= this.materiaTotal) {
					this.hasLoaded = true;
					this.hasComplete();
				}
			});
		}
	}
}
var WardrobeLoader = new WardrobeLoaderClass();
