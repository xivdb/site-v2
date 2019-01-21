//
// Content: Achievements
//
class ContentAchievementsClass
{
	// get achievements
	get(id, callback)
	{
		setTimeout(() => {
			$.ajax({
				url: `/achievement/${id}/xivsync/census`,
				cache: true,
				success: data => {
					callback(data);
				},
				error: (response, status, error) => {
					console.error('Achievement fetch error: ', response.responseText);
				}
			});
		},
		1000);
	}

	//
	// Render on content pages
	//
	render($dom, data)
	{
		console.log(data);
		html = [];

		if (data.eligable > 0)
		{
			html.push('<h4>Obtain Rate</h4>');

			// percent who have it
			html.push(`
				<div class="as-row as-row-flex">
					<div>
						${data.obtained_percent}%
					</div>
					<div style="margin:-8px 0 10px 0;">
						<div style="font-size:14px;"><strong>${number_format(data.obtained)}</strong> of ${number_format(data.eligable)}</div> 
						eligible characters have earned this achievement.
					</div>
				</div>
			`);

			// graph key
			html.push(`
				<div class="as-release-date">Achievement release date: ${data.release_date}</div>
			`);
		}
		else
		{
			html.push('No characters on XIVSync have obtained this achievement.');
			html.push(`<div class="as-none">Achievement released: ${data.release_date}</div>`);
		}

		$dom.html(html.join(''));
	}
}
var ContentAchievements = new ContentAchievementsClass();
