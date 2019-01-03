import { app } from '../../../Application';
import { InteractiveTable } from '../../../components/InteractiveTable';

function getFilters(): any {
    const filters = $('#pages-filters');

    return {
        type: {
            operator: 'in',
            value: filters.find('[name="types[]"]').val(),
        },

        routeUrl: {
            operator: 'expression',
            value: filters.find('[name="url"]').val(),
        },

        websiteId: {
            operator: 'in',
            value: filters.find('[name="website_ids[]"]').val(),
        },

        status: {
            operator: 'in',
            value: filters.find('[name="statuses[]"]').val(),
        },
    };
}

app.addViewInitializer('backend.pages.index', () => {
    const dataTable = new InteractiveTable({
        autofocus: true,
        loaderSelector: '#pages-loader',
        source: '/api/pages',
        tableSelector: '#pages-table',

        onPrepareRequest(request) {
            return Object.assign(request, {
                filters: getFilters(),
            });
        },
    });

    $('#pages-filters').on('change', () => {
        dataTable.refresh();
    });

    dataTable.refresh();
});
