import SimpleMDE from 'simplemde';

import CheckboxComponent from '../../../../../base/components/CheckboxComponent';
import InputComponent from '../../../../../base/components/InputComponent';

export default class PageSection {

    /**
     * @param {Bus} bus
     * @param {jQuery} $container
     */
    constructor(bus, $container) {
        this.$dom = {
            form: $container.find('form'),
            isDisabledAlert: $container.find('.is-disabled-alert'),
        };

        this.$form = {
            isEnabled: new CheckboxComponent(
                $container.find('[name="is_enabled"]'),
            ),

            id: new InputComponent(
                $container.find('[name="id"]'),
            ),

            languageId: new InputComponent(
                $container.find('[name="language_id"]'),
            ),

            url: new InputComponent(
                $container.find('[name="url"]'),
            ),

            title: new InputComponent(
                $container.find('[name="title"]'),
            ),

            tagIds: new InputComponent(
                $container.find('[name="tag_ids"]'),
            ),

            status: new InputComponent(
                $container.find('[name="status"]'),
            ),

            lead: new InputComponent(
                $container.find('[name="lead"]'),
            ),

            content: new InputComponent(
                $container.find('[name="content"]'),
            ),
        };

        // When user clicks on the "is enabled" checkbox, let's re-render ourselves
        this.$form.isEnabled.on('change', () => {
            this.$refresh();
        });

        this.$dom.form.on('change', () => {
            bus.emit('form::changed');
        });

        // When user presses enter when inside the URL or title inputs, consider it equal to clicking the "submit"
        // button
        this.$dom.form.on('keypress', 'input', (evt) => {
            if (evt.originalEvent.code === 'Enter') {
                bus.emit('form::submit');
            }
        });

        // Initialize SimpleMDE
        this.$simpleMde = new SimpleMDE({
            autoDownloadFontAwesome: false,
            element: this.$dom.form.find('[name="content"]')[0],
            forceSync: true,
        });

        // When user changes a tab, we may have to refresh ourselves. since SimpleMDE - after becoming visible - tends
        // to forget that it should repaint
        bus.on('tabs::changed', ({ activatedTabName }) => {
            if (activatedTabName.includes('page')) {
                this.$refresh();
            }
        });

        this.$refresh();
    }

    /**
     * @returns {object}
     */
    serialize() {
        const form = this.$form;

        return {
            id: form.id.getValue(),
            language_id: form.languageId.getValue(),
            url: form.url.getValue(),
            title: form.title.getValue(),
            tag_ids: form.tagIds.getValue(),
            status: form.status.getValue(),
            lead: form.lead.getValue(),
            content: form.content.getValue(),
        };
    }

    /**
     * @returns {boolean}
     */
    isEnabled() {
        return true;
    }

    /**
     * @private
     *
     * Re-renders the component.
     */
    $refresh() {
        this.$toggle(
            this.isEnabled(),
        );

        if (this.isEnabled()) {
            this.$form.title.focus();
            this.$simpleMde.codemirror.refresh();
        }
    }

    /**
     * @private
     *
     * Shows / hides the component.
     *
     * @param {boolean} enabled
     */
    $toggle(enabled) {
        this.$dom.form.toggle(enabled);
        this.$dom.isDisabledAlert.toggle(!enabled);
    }

}