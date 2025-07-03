const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { createElement } = wp.element;
const { useBlockProps } = wp.blockEditor; // On importe le hook essentiel

const blockName = 'esb/effi-share-buttons';

registerBlockType(blockName, {
	edit: function () {
        // On appelle le hook pour récupérer les propriétés nécessaires
		const blockProps = useBlockProps({
			style: {
				border: '1px dashed #ccc',
				padding: '16px',
			},
		});

        // On applique les propriétés à notre élément principal avec { ...blockProps }
		return createElement(
			'div',
			{ ...blockProps }, // <-- C'est LA modification la plus importante
			__('effi Share Buttons Block', 'effi-share-buttons'),
			createElement(
				'p',
				{
					style: {
						fontSize: '12px',
						fontStyle: 'italic',
						margin: '8px 0 0 0',
					},
				},
				__(
					'The buttons are configured in the global settings and will appear on the live site.',
					'effi-share-buttons'
				)
			)
		);
	},

	save: function () {
		return null;
	},
});