import { __ } from '@wordpress/i18n';

import { useBlockProps, InspectorControls, RichText, PanelColorSettings } from '@wordpress/block-editor';
import { FontSizePicker, PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

export default function Edit( { attributes, setAttributes } ) {
	const fontSizes = [
		{ name: __( 'Small' ), slug: 'small', size: 16 },
		{ name: __( 'Medium' ), slug: 'medium', size: 28 },
		{ name: __( 'Large' ), slug: 'large', size: 40 },
		{ name: __( 'Extra Large' ), slug: 'xlarge', size: 52 },
	];

	const alignmentOptions = [
		{ label: 'Right', value: 'right' },
		{ label: 'Left', value: 'left' },
		{ label: 'Center', value: 'center' },
	];

	return (
		<div { ...useBlockProps() }>
			<Fragment>
				<InspectorControls>
					<PanelBody title="Sheet Settings" initialOpen={ true }>
						<TextControl
							label="Google Sheet CSV URL"
							value={ attributes.sheetCSVURL }
							onChange={ ( newURL ) =>
								setAttributes( { sheetCSVURL: newURL } )
							}
							help="The link to a CSV of your google sheet. Can be obtained to File > Share > Publish to the Web > Change from Web Page to Comma Separated Values > Publish"
						/>

						<TextControl
							label="Cell Coordinate"
							value={ attributes.dataLocation }
							onChange={ ( newCoordinate ) =>
								setAttributes( { dataLocation: newCoordinate } )
							}
							help="The coordinate to pull data from, example: A1"
						/>
					</PanelBody>

					<PanelBody title="Text Formatting" initialOpen={ true }>
						<b style={ { fontSize: 11 } }>DESCRIPTION FONT SIZE</b>
						<FontSizePicker
							fontSizes={ fontSizes }
							value={ attributes.descriptionSize }
							onChange={ ( newFontSize ) =>
								setAttributes( {
									descriptionSize: newFontSize,
								} )
							}
						/>

						<b style={ { fontSize: 11 } }>DATA FONT SIZE</b>
						<FontSizePicker
							fontSizes={ fontSizes }
							value={ attributes.dataSize }
							onChange={ ( newFontSize ) =>
								setAttributes( { dataSize: newFontSize } )
							}
						/>

						<SelectControl
							label="Description Alignment"
							value={ attributes.descriptionAlignment }
							options={ alignmentOptions }
							help="The horizontal alignment of the description text"
							onChange={ ( newAlignment ) =>
								setAttributes( {
									descriptionAlignment: newAlignment,
								} )
							}
						/>

						<SelectControl
							label="Data Alignment"
							value={ attributes.dataAlignment }
							options={ alignmentOptions }
							help="The horizontal alignment of the google sheets data"
							onChange={ ( newAlignment ) =>
								setAttributes( { dataAlignment: newAlignment } )
							}
						/>

						<PanelColorSettings
							title={ __( 'Color Settings' ) }
							colorSettings={ [
								{
									label: __( 'Description Text Color' ),
									onChange: ( newColor ) =>
										setAttributes( {
											descriptionColor: newColor,
										} ),
									value: attributes.descriptionColor,
								},
								{
									label: __( 'Data Text Color' ),
									onChange: ( newColor ) =>
										setAttributes( {
											dataColor: newColor,
										} ),
									value: attributes.dataColor,
								},
							] }
						/>
					</PanelBody>
				</InspectorControls>
			</Fragment>

			<RichText // Description text
				tagName="p"
				style={ {
					textAlign: attributes.descriptionAlignment,
					fontSize: attributes.descriptionSize,
					color: attributes.descriptionColor,
					marginBottom: 0,
					paddingBottom: 0,
				} }
				onChange={ ( newDescription ) => {
					setAttributes( { description: newDescription } );
				} }
				value={ attributes.description }
			/>

			<p
				style={ {
					textAlign: attributes.dataAlignment,
					fontSize: attributes.dataSize,
					color: attributes.dataColor,
					marginTop: 0,
					paddingTop: 0,
				} }
			>
				#,###,###
			</p>
		</div>
	);
}
