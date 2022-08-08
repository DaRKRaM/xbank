import { registerBlockType } from '@wordpress/blocks';
import { RichText, InspectorControls, BlockControls, AlignmentToolbar } from '@wordpress/block-editor';
import { Disabled, PanelBody, PanelRow, SelectControl } from '@wordpress/components';
import { ServerSideRender } from '@wordpress/editor';
import { Component, Fragment } from '@wordpress/element';
import { withSelect, select } from '@wordpress/data';
import { __, _e } from '@wordpress/i18n';

const plugin = 'acf-frontend-form-element';

class FormBlock extends Component {

    constructor(props) {
		super(props);
		this.state = {
			editMode: true,
            formID: 0
		}
	}
 
	getInspectorControls = () => {
		const { attributes, setAttributes } = this.props;
 
        
        let choices = [];
        if (this.props.posts) {
            choices.push({ value: 0, label: __( 'Select a form', plugin ) });
            this.props.posts.forEach(post => {
                choices.push({ value: post.id, label: post.title.rendered });
            });
        } else {
            choices.push({ value: 0, label: __( 'Loading...', plugin ) })
        }

		return (
			<InspectorControls 
                key='fea-inspector-controls'
                >
                <PanelBody
						title={__("Form Settings", plugin )}
						initialOpen={true}
					>
						<PanelRow>
                        <SelectControl
                            label={__('Form', plugin )}
                            options={choices}
                            value={attributes.formID}
                            onChange={(newval) => setAttributes({ formID: parseInt(newval) })}
                        />
                        </PanelRow>
                </PanelBody>
			</InspectorControls>
		);
	}
 
	getBlockControls = () => {
        const { attributes, setAttributes } = this.props;        
        let choices = [];
        if (this.props.posts) {
            choices.push({ value: 0, label: __( 'Select a form', plugin ) });
            this.props.posts.forEach(post => {
                choices.push({ value: post.id, label: post.title.rendered });
            });
        } else {
            choices.push({ value: 0, label: __( 'Loading...', plugin ) })
        }
        return (
            <BlockControls 
                key='fea-block-controls'
                >
                <SelectControl
                    options={choices}
                    value={attributes.formID}
                    onChange={(newval) => setAttributes({ formID: parseInt(newval) })}
                />                       
            </BlockControls>
        );
    }
 
	render() {
        const { attributes, setAttributes } = this.props;
        const alignmentClass = (attributes.textAlignment != null) ? 'has-text-align-' + attributes.textAlignment : '';
        return ([
            this.getInspectorControls(),
            this.getBlockControls(),
            <Disabled 
                key='fea-disabled-render'
                >
                <ServerSideRender
                    block={this.props.name}
                    attributes={{ 
                        formID: attributes.formID,
                        editMode: this.state.editMode
                    }}
                />
            </Disabled>            

        ]);
    }
}
 
registerBlockType('acf-frontend/form', {
	title: __('Frontend Form', plugin ),
	category: 'acf-frontend',
	icon: 'feedback',
	description: __('Display a frontend admin form so that your users can update content from the frontend.', plugin ),
	keywords: ['frontend editing', 'admin form'],
	attributes: {
        formID: {
            type: 'number'
        },
        editMode: {
            type: 'boolean',
            default: true,
        }
    },
	edit: withSelect(select => {
        const query = {
            per_page: -1,
            status: 'any',
        }
        return {
            posts: select('core').getEntityRecords('postType', 'admin_form', query)
        }
    })(FormBlock),
    save: () => { return null }

});

registerBlockType('acf-frontend/submissions', {
	title: __('Frontend Submissions', plugin ),
	category: 'acf-frontend',
	icon: 'feedback',
	description: __('Display frontend submissions so that site admins can update content from the frontend.', plugin ),
	keywords: ['frontend editing', 'admin form'],
	attributes: {
        formID: {
            type: 'number'
        },
        editMode: {
            type: 'boolean',
            default: true,
        }
    },
	edit: withSelect(select => {
        const query = {
            per_page: -1,
            status: 'any',
        }
        return {
            posts: select('core').getEntityRecords('postType', 'admin_form', query)
        }
    })(FormBlock),
    save: () => { return null }

});