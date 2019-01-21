//
// Game data functionality
//
var durations = [];
class GameDataClass
{
    constructor()
    {
        this.list = [];
        this.start = null;
    }

    //
    // Process all tables
    //
    processAll()
    {
        $('.status1').html('Waiting ...');

        $('.table tr[data-name]').each((i, element) => {
            var table = $(element).attr('data-name');
            this.list.push(table);
        });

        this.processAllAction();
    }

    //
    // Process all action, allows recurrsion
    //
    processAllAction()
    {
        // if still tables
        if (this.list.length > 0) {
            // get first and shift it off
            var table = this.list[0];
            this.list.shift();

            // log
            console.log('Processing:', table, 'Remaining:', this.list.length);

            // run
            this.processSingle(table, () => {
                this.processAllAction();
            });

            return;
        }

        console.log('Complete!');
    }

    //
    // Ajax request a single process
    //
    processSingle(name, callback)
    {
        var $row = $(`table tr.row-${name}`);
        $row.find('.status1').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Game Files...');

        // scroll to it
        var rowpos = $row.position().top-400;
        $('body').scrollTop(rowpos);

        $.ajax({
            url: `/gamedata/import/${name}/process`,
            cache: false,
            dataType: 'json',
            method: 'GET',
            success: function(log)
            {
                // set log
                log = log.length > 0 ? log.join('<br>') : 'Nothing to insert';

                // add status
                $row.find('td').addClass('highlight-success');
                $row.find('.status1').html('<i class="fa fa-check"></i> Complete!');
                $row.after(`<tr><td colspan="5" class="import-log">${log}</td></tr>`);

                // if call back
                if (callback) {
                    callback();
                }
            },
            error: function(data, status, error)
            {
                $row.find('td').addClass('highlight-error');
                $row.find('.status1').html('<i class="fa fa-times"></i> Failed');
                console.error(data, status, error);
            }
        });
    }

    //
    // Parse lodestone
    //
    runLodestone()
    {
        var list = items;
        this.runLodestoneAction(list, list.length, []);
        $('.ls-verify .progress').attr('value', 0).text(0 + '%');
    }

    //
    // Process lodestone items action
    //
    runLodestoneAction(list, total, results)
    {
        var item = list[0];
        list.shift();

        var name = item.name_en.replace(' ', '+');
        var url = `https://localhost:3000/database/item/search?name=${name}&c=1497705693&language=en`;


        var count = total - list.length,
            percent = (count / total) * 100;

        $('.ls-verify .setup-status').html('<i class="fa fa-circle-o-notch fa-spin"></i> ('+ count +'/'+ total +') '+ item.id +' - ' + item.name_en);

        var start = +new Date();

        $.ajax(
        {
            url: url,
            data: {
                count: list.length,
                total: total,
                lsid: item.lodestone_id,
            },
            success: function(data)
            {
                if (!data.error && data.results[0] && typeof data.results[0].id !== 'undefined')
                {
                    for(let i in data.results) {
                        let result = data.results[i];

                        if (item.name_en.toLowerCase() == result.name.toLowerCase()) {
                            data = result;
                            break;
                        }
                    }

                    if (!data) {
                        console.log('Failed on: ' + item.id + ' ' + item.name);
                        return GameData.runLodestoneAction(list, total, results);
                    }

                    // get data page
                    return GameData.RunLodestoneActionDetails(data, item, list, total, results);

                    // return GameData.runLodestoneAction(list, total);
                } else {
                    console.log('Failed on: ' + item.id + ' ' + item.name);
                    return GameData.runLodestoneAction(list, total, results);
                }
            },
            error: function(data, status, error)
            {
                $('.ls-verify .progress').attr('value', 0).text(0 + '%');
                $('.ls-verify .setup-status').html('Error, check console');

                // console error
                console.error(status, error);
                console.log(data.responseText);
            },
        });
    }

    RunLodestoneActionDetails(result, item, list, total, results)
    {
        let url = `https://localhost:3000/database/item/get/${result.id}`;
        console.log('Doing detailed parse for: ' + result.name);

        $.ajax({
            url: url,
            success: function(data) {
                let image = data.icon.split('.png')[0];
                image = image.replace('https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/', '');

                let string = $('#lodestone-results').val();
                string += `UPDATE xiv_items SET lodestone_id = '${result.id}', icon_lodestone = '${image}', parsed_lodestone = 1, parsed_lodestone_time = NOW() WHERE id = ${item.id};` + "\n";
                $('#lodestone-results').val(string);

                results.push(item.id);
                console.log('Completed ' + item.id);

                if (results.length < 3000) {
                    return GameData.runLodestoneAction(list, total, results);
                } else {
                    console.log('finished');
                    return;
                }

                return GameData.runLodestoneAction(list, total, results);
            },
            error: function(data) {
                console.log('Could not get item: ' + item.id);
                return GameData.runLodestoneAction(list, total, results);
            }
        })
    }

    //
    // Run connections
    //
    runConnections()
    {
        var list = [];

        for (var i = 0; i < size; i++) {
            list.push(i);
        }

        $('.dataconnections .progress').attr('value', 0).text(0 + '%');
        $('.dataconnections .setup-status').html('Starting, ' + list.length + ' to process.');

        this.runConnectionsAction(list, list.length);
    }

    //
    // Parse connections
    //
    runConnectionsAction(list, total)
    {
        var num = list[0];
        list.shift();

        var url = '/gamedata/gamesetup/connections/' + num,
            count = total - list.length,
            percent = (count / total) * 100;

        $.ajax(
        {
            url: url,
            success: function(data)
            {
                if (data)
                {
                    $('.dataconnections .progress').attr('value', percent).text(percent + '%');
                    $('.dataconnections .setup-status').html('<i class="fa fa-circle-o-notch fa-spin"></i> Completed: ('+ count +'/'+ total +') ' + data);

                    if (list.length > 0) {
                        GameData.runConnectionsAction(list, total);
                        return;
                    }
                    else
                    {
                        $('.dataconnections .setup-status').html('<i class="fa fa-check"></i> Completed! '+ count +'/'+ total);
                        return;
                    }
                }

                console.error('Unknown error?');
                console.log(data);
            },
            error: function(data, status, error)
            {
                $('.dataconnections .progress').attr('value', 0).text(0 + '%');
                $('.dataconnections .setup-status').html('Error, check console');

                // console error
                console.error(status, error);
                console.log(data.responseText);
            },
        });
    }
}

var GameData = new GameDataClass();
