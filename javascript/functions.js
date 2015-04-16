function getManufacturers() {
    var api = new Api();
    var response = api.getManufacturers();
    var html = '';

    manufacturers = response['data'];

    for (var manufacturer in manufacturers) {
        if (manufacturers.hasOwnProperty(manufacturer)) {
            var m = manufacturers[manufacturer];
        }
        html = html + 'manufacturer_id: ' + m.manufacturer_id + ', manufacturer: ' + m.manufacturer + '<br/>';
    }
    return html;
}

function getManufacturer() {
    var api = new Api();

    var manufacturer = document.getElementById('manufacturer_1').value;
    var response = api.getManufacturer(manufacturer);
    var html = '';

    models = response['data']['models'];

    for (var model in models) {
        if (models.hasOwnProperty(model)) {
            var m = models[model];
        }
        html = html + 'model_id: ' + m.model_id + ', model: ' + m.model + ', subbody:' + m.subbody + '<br/>';
    }
    return html;
}

function getVehicles() {
    var api = new Api();

    var manufacturer = document.getElementById('manufacturer_2').value;
    var model_id = document.getElementById('model_id').value;
    var response = api.getVehicles(manufacturer, model_id);
    var html = '<table>';

    models = response['data'];

    for (var model in models) {
        if (models.hasOwnProperty(model)) {
            var m = models[model];
        }
        html = html + '<tr><td>';
        html = html + '<b>manufacturer: ' + m.manufacturer + '</b><br/>';
        html = html + 'manufacturer_id: ' + m.manufacturer_id + '<br/>';
        html = html + 'model: ' + m.model + '<br/>';
        html = html + 'model_id: ' + m.model_id + '<br/>';
        html = html + 'subbody: ' + m.subbody + '<br/>';
        html = html + 'litres: ' + m.litres + '<br/>';
        html = html + 'fuel: ' + m.fuel + '<br/>';
        html = html + 'extra_info: ' + m.extra_info + '<br/>';
        html = html + 'enginecode: ' + m.enginecode + '<br/>';
        html = html + 'kw: ' + m.kw + '<br/>';
        html = html + 'tuning: ' + m.tuning + '<br/>';
        html = html + 'rpm: ' + m.rpm + '<br/>';
        html = html + 'din_hp: ' + m.din_hp + '<br/>';
        html = html + 'start_year: ' + m.start_year + '<br/>';
        html = html + 'end_year: ' + m.end_year + '<br/>';
        html = html + 'mid: ' + m.mid + '<br/>';
        html = html + '</td></tr>';

    }
    html = html + '</table>';
    return html;
}

function getVehicle() {
    var api = new Api();

    var mid = document.getElementById('mid_1').value;
    return '<code>' + api.getVehicle(mid) + '</code>';
}

function getRepair() {
    var api = new Api();

    var mid = document.getElementById('mid_2').value;
    return '<code>' + api.getRepair(mid) + '</code>';
}

function getRepairTimes() {
    var api = new Api();

    var mid = document.getElementById('mid_3').value;
    var repair_times_id = document.getElementById('repair_times_id').value;
    var parts = document.getElementById('parts').value;
    return '<code>' + api.getRepairTimes(mid,repair_times_id,parts) + '</code>';
}

function getService() {
    var api = new Api();

    var mid = document.getElementById('mid_4').value;
    return '<code>' + api.getService(mid) + '</code>';
}

function getServiceSchedule() {
    var api = new Api();

    var mid = document.getElementById('mid_5').value;
    var variant_id = document.getElementById('variant_id').value;
    var parts = document.getElementById('parts_2').value;
    return '<code>' + api.getServiceSchedule(mid, variant_id, parts) + '</code>';
}

function getServiceIntervals() {
    var api = new Api();

    var mid = document.getElementById('mid_6').value;
    var variant_id = document.getElementById('variant_id_2').value;
    var interval_id = document.getElementById('interval_id').value;
    return '<code>' + api.getServiceIntervals(mid, variant_id, interval_id) + '</code>';
}