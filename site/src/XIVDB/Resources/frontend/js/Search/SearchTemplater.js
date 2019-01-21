//
// Search engine templater, to replace handlebars
//
class SearchTemplaterClass
{
	//
	// Search result entities
	//
	templateSearchEntity(data)
	{
		var html = `
		<div class="entity {{color1}}" data-xivdb-seturlname="0" data-xivdb-seturlcolor="0" data-xivdb-seturlicon="0" data-id="${data.id}">
	        <a class="icon" href="${data.url}" data-xivdb-parent=".entity"><img src="/img/loader/loading2.gif" data-src="${data.icon}"></a>
	        <div class="data">
	            <div class="name {{color2}}"><a href="${data.url}" data-xivdb-parent=".entity">${data.name}</a></div>
	            <div class="extra">${data.extra}</div>
	        </div>
	    </div>`;

		html = html.replace('{{color1}}', data.color ? `entity-rarity-${data.color}` : '');
		html = html.replace('{{color2}}', data.color ? `rarity-${data.color}` : '');

		return html;
	}

	//
	// Search tabs
	//
	templateSearchTab(data)
	{
		var html = `<span data-category="${data.category}">
			<i>${data.total}</i> ${data.title}
		</span>`;

		return html;
	}

	//
	// Attribute filter bubble
	//
	templateFilterAttributeBLock(data)
	{
		var html = `
			<span class="filter-param-attribute-block" data-filter-value="${data.id}_${data.condition}" data-tt="${languages.custom(716)}">
		        <span class="filter-param-attribute-name"><em>${data.quality}</em> ${data.name}</span>
		        <span class="filter-param-attribute-condition">${data.conditionSymbol}</span>
		        <span class="filter-param-attribute-value">${data.value}</span>
		    </span>
		`;

		return html;
	}

	//
	// Paging
	//
	templateSearchPaging(data)
	{
		return `
			<div class="search-one-paging" data-category="${data.category}">
                <div class="search-paging-container">
					<span class="left">
						<button type="button" data-tt="Go the first page" data-page="1">${languages.custom(717)}</button>
						<button type="button" data-tt="Go the previous page"><i class="fa fa-chevron-left"></i></button>
					</span>
					<span class="center">[[PAGES]]</span>
					<span class="right">
						<button type="button" data-tt="Go to the next page"><i class="fa fa-chevron-right"></i></button>
						<button type="button" data-tt="Go to the last page">${languages.custom(718)}</button>
					</span>
		        </div>

		        <div class="search-info">
		            ${data.category}
		        </div>
		    </div>
		`;
	}

	//
	// Paging button
	//
	templateSearchPagingButton(data)
	{
		var active = data.active ? 'active' : '';
		return `<button type="button" class="${active}" data-page="${data.page}">${data.page}</button>`;
	}

	templateSearchInfo1(data)
	{
		return `<span>${data.total}</span> results`;
	}

	templateSearchInfo2(data)
	{
		var html = `
		<span>${data.category}</span>
	    &nbsp;-&nbsp;
		${languages.custom(936)}
	    &nbsp;-&nbsp;
		${languages.custom(937)}
	    <em>(${data.duration}s)</em>
		`;

		html = html.replace('{{ found }}', data.found);
		html = html.replace('{{ total }}', data.total);
		html = html.replace('{{ page }}', data.page);
		html = html.replace('{{ pages }}', data.pages);

		return html;
	}
}

var SearchTemplater = new SearchTemplaterClass();
