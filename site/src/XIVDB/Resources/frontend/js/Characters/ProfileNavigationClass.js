//
// Handle the main navigation
//
class ProfileNavigationClass
{
	watch()
	{
		//
		// Switching the main navigation on the left
		//
		$('html').on('click', '.cp-nav > button', event => {
			var id = $(event.currentTarget).attr('data-id');
			this.switchTab(id);
			$(window).trigger('lookup');
		});

		//
		// Switching class on the class/job list
		//
		$('html').on('click', '.character-classes .cc-list button', event => {
			var id = $(event.currentTarget).attr('data-cc');
			this.switchClassesPage(id);
			$(window).trigger('lookup');
		});

		//
		// Switching gearset
		//
		$('html').on('click', '.character-gearsets .gs-list button', event => {
			var id = $(event.currentTarget).attr('data-gs');
			this.switchGearsetsPage(id);
			$(window).trigger('lookup');
		});

		//
		// Switching timeline date
		//
		$('html').on('click', '.character-timeline .tl-list button', event => {
			var id = $(event.currentTarget).attr('data-tl');
			this.switchTimelinePage(id);
			$(window).trigger('lookup');
		});

		//
		// Switching achievement category
		//
		$('html').on('click', '.character-achievements .ac-list button', event => {
			var id = $(event.currentTarget).attr('data-ac');
			this.switchAchievementsPage(id);
			$(window).trigger('lookup');
		});
	}

	//
	// Switch tab
	//
	switchTab(id)
	{
		// remove active states
		$('.cp-nav > button.active, .cp-content > div.active').removeClass('active');

		// set active states
		$(`.cp-nav > button[data-id="${id}"], .cp-content > div[data-id="${id}"]`).addClass('active');

		// get tooltips
        if ((typeof XIVDBTooltips !== 'undefined') && !isOnMobile()) {
            XIVDBTooltips.get();
        }
	}

	//
	// Switch classes page
	//
	switchClassesPage(id)
	{
		// remove active states
		$('.character-classes .cc-page.active, .character-classes .cc-list button.active').removeClass('active');

		// set active states
		$(`.character-classes .cc-page[data-cc="${id}"], .character-classes .cc-list button[data-cc="${id}"]`).addClass('active');

		// get tooltips
		if ((typeof XIVDBTooltips !== 'undefined') && !isOnMobile()) {
			XIVDBTooltips.get();
		}
	}

	//
	// Switch gearsets page
	//
	switchGearsetsPage(id)
	{
		// remove active states
		$('.character-gearsets .gs-page.active, .character-gearsets .gs-list button.active').removeClass('active');

		// set active states
		$(`.character-gearsets .gs-page[data-gs="${id}"], .character-gearsets .gs-list button[data-gs="${id}"]`).addClass('active');

		// get tooltips
		if ((typeof XIVDBTooltips !== 'undefined') && !isOnMobile()) {
			XIVDBTooltips.get();
		}
	}

	//
	// Switch timeline!
	//
	switchTimelinePage(id)
	{
		var idSplit = id.split(','),
			year = idSplit[0],
			month = idSplit[1];

		// remove active states
		$('.character-timeline').find('.tl-page.active, .tl-list.active, .tl-list button.active').removeClass('active');

		// set active states
		$('.character-timeline').find(`.tl-page[data-tl="${id}"], .tl-list[data-tl="${year}"], .tl-list .tl-list-months button[data-tl="${id}"]`).addClass('active');

		// get tooltips
		if ((typeof XIVDBTooltips !== 'undefined') && !isOnMobile()) {
			XIVDBTooltips.get();
		}
	}

	//
	// Switch achievements page!
	//
	switchAchievementsPage(id)
	{
		// remove active states
		$('.character-achievements').find('.ac-page.active, .ac-list button.active').removeClass('active');

		// set active states
		$('.character-achievements').find(`.ac-page[data-ac="${id}"], .ac-list button[data-ac="${id}"]`).addClass('active');

		// get tooltips
		if ((typeof XIVDBTooltips !== 'undefined') && !isOnMobile()) {
			XIVDBTooltips.get();
		}

		// scroll top as list is long
		if ($(document).scrollTop() > 400) {
			$('body').scrollTop(220);
		}

	}
}
