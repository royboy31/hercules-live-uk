import { config, fields, singleton, collection } from '@keystatic/core';

export default config({
  storage: {
    kind: 'local',
  },

  singletons: {
    globalConfig: singleton({
      label: 'Global Config',
      path: 'src/content/keystatic/global-config',
      format: { data: 'json' },
      schema: {
        siteName: fields.text({ label: 'Site Name' }),
        phone: fields.text({ label: 'Phone Number' }),
        email: fields.text({ label: 'Email Address' }),
        address: fields.object({
          streetAddress: fields.text({ label: 'Street Address' }),
          postalCode: fields.text({ label: 'Postal Code' }),
          addressLocality: fields.text({ label: 'City' }),
          addressCountry: fields.text({ label: 'Country Code' }),
        }, { label: 'Address' }),
        openingHours: fields.array(
          fields.object({
            day: fields.text({ label: 'Day' }),
            time: fields.text({ label: 'Time (e.g. 09:00-17:00)' }),
          }),
          { label: 'Opening Hours', itemLabel: (props) => props.fields.day.value }
        ),
        socialLinks: fields.array(
          fields.object({
            platform: fields.text({ label: 'Platform' }),
            url: fields.text({ label: 'URL' }),
          }),
          { label: 'Social Links', itemLabel: (props) => props.fields.platform.value }
        ),
        twitterHandle: fields.text({ label: 'Twitter Handle' }),
        defaultDescription: fields.text({ label: 'Default Meta Description', multiline: true }),
      },
    }),

    heroSlider: singleton({
      label: 'Homepage Hero Slider',
      path: 'src/content/keystatic/hero-slider',
      format: { data: 'json' },
      schema: {
        tagline: fields.text({ label: 'H1 Tagline' }),
        subheading: fields.text({ label: 'Subheading' }),
        slides: fields.array(
          fields.object({
            heading: fields.text({ label: 'Heading (HTML allowed)', multiline: true }),
            description: fields.text({ label: 'Description' }),
            buttonText: fields.text({ label: 'Button Text' }),
            buttonLink: fields.text({ label: 'Button Link' }),
            image: fields.text({ label: 'Desktop Image Path (1280x550)' }),
            imageMobile: fields.text({ label: 'Tablet Image Path' }),
            imageMobileSm: fields.text({ label: 'Mobile Image Path' }),
          }),
          {
            label: 'Slides',
            itemLabel: (props) => props.fields.buttonText.value || 'Untitled Slide',
          }
        ),
      },
    }),

    homepageSections: singleton({
      label: 'Homepage Sections',
      path: 'src/content/keystatic/homepage-sections',
      format: { data: 'json' },
      schema: {
        whyChooseUs: fields.object({
          sectionHeading: fields.text({ label: 'Section Heading' }),
          highlightWord: fields.text({ label: 'Highlighted Word (shown in blue)' }),
          features: fields.array(
            fields.object({
              title: fields.text({ label: 'Title' }),
              description: fields.text({ label: 'Description', multiline: true }),
              iconSvg: fields.text({ label: 'SVG Icon Code', multiline: true }),
            }),
            { label: 'Feature Boxes', itemLabel: (props) => props.fields.title.value || 'Untitled' }
          ),
        }, { label: 'Why Choose Us' }),

        designService: fields.object({
          heading: fields.text({ label: 'Heading' }),
          headingHighlight: fields.text({ label: 'Highlighted Part (shown in blue)' }),
          paragraphs: fields.array(
            fields.text({ label: 'Paragraph', multiline: true }),
            { label: 'Description Paragraphs' }
          ),
          ctaText: fields.text({ label: 'CTA Button Text' }),
          mainImage: fields.text({ label: 'Main Image Path' }),
        }, { label: 'Design Service Section' }),

        herculesMerchandise: fields.object({
          heading: fields.text({ label: 'Heading' }),
          headingHighlight: fields.text({ label: 'Highlighted Part (shown in blue)' }),
          paragraphs: fields.array(
            fields.text({ label: 'Paragraph (HTML allowed)', multiline: true }),
            { label: 'Description Paragraphs' }
          ),
          ctaText: fields.text({ label: 'CTA Button Text' }),
          aboutImage: fields.text({ label: 'About Image Path' }),
          aboutImageAlt: fields.text({ label: 'About Image Alt Text' }),
        }, { label: 'Hercules Merchandise About' }),
      },
    }),

    footer: singleton({
      label: 'Footer',
      path: 'src/content/keystatic/footer',
      format: { data: 'json' },
      schema: {
        tagline: fields.text({ label: 'Logo Tagline' }),
        footerColumns: fields.array(
          fields.object({
            heading: fields.text({ label: 'Column Heading' }),
            links: fields.array(
              fields.object({
                label: fields.text({ label: 'Link Label' }),
                href: fields.text({ label: 'Link URL' }),
              }),
              { label: 'Links', itemLabel: (props) => props.fields.label.value || 'Untitled' }
            ),
          }),
          { label: 'Footer Columns', itemLabel: (props) => props.fields.heading.value || 'Untitled' }
        ),
        resellerHeading: fields.text({ label: 'Reseller Heading' }),
        resellerText: fields.text({ label: 'Reseller Description' }),
        resellerFeatures: fields.array(
          fields.object({
            icon: fields.text({ label: 'Icon (tag | headset)' }),
            label: fields.text({ label: 'Feature Label' }),
          }),
          { label: 'Reseller Features', itemLabel: (props) => props.fields.label.value || 'Untitled' }
        ),
        resellerButtonText: fields.text({ label: 'Reseller Button Text' }),
        helpHeading: fields.text({ label: 'Help Column Heading' }),
        followHeading: fields.text({ label: 'Follow Us Heading' }),
        socialLinks: fields.array(
          fields.object({
            platform: fields.text({ label: 'Platform (linkedin | instagram | facebook | youtube)' }),
            href: fields.text({ label: 'Profile URL' }),
          }),
          { label: 'Social Links', itemLabel: (props) => props.fields.platform.value || 'Untitled' }
        ),
        contactButtonText: fields.text({ label: 'Contact Button Text' }),
        phone: fields.text({ label: 'Phone Number (tel: format)' }),
        phoneDisplay: fields.text({ label: 'Phone Display Text' }),
        copyright: fields.text({ label: 'Copyright Text' }),
        legalLinks: fields.array(
          fields.object({
            label: fields.text({ label: 'Link Label' }),
            href: fields.text({ label: 'Link URL' }),
          }),
          { label: 'Legal Links', itemLabel: (props) => props.fields.label.value || 'Untitled' }
        ),
      },
    }),
  },

  collections: {
    featuredProducts: collection({
      label: 'Featured Products',
      slugField: 'slug',
      path: 'src/content/keystatic/featured-products/*',
      format: { data: 'json' },
      schema: {
        name: fields.text({ label: 'Product Name', validation: { isRequired: true } }),
        slug: fields.text({ label: 'URL Slug', validation: { isRequired: true } }),
        productId: fields.integer({ label: 'WooCommerce Product ID' }),
        image: fields.text({ label: 'Product Image Path' }),
        alt: fields.text({ label: 'Image Alt Text' }),
        sortOrder: fields.integer({ label: 'Sort Order', defaultValue: 0 }),
      },
    }),

    menuCategories: collection({
      label: 'Menu Categories',
      slugField: 'categoryKey',
      path: 'src/content/keystatic/menu-categories/*',
      format: { data: 'json' },
      schema: {
        categoryKey: fields.text({ label: 'Category Key' }),
        categoryLabel: fields.text({ label: 'Display Label' }),
        items: fields.array(
          fields.object({
            label: fields.text({ label: 'Label' }),
            href: fields.text({ label: 'URL' }),
            iconUrl: fields.text({ label: 'Icon URL' }),
          }),
          { label: 'Menu Items', itemLabel: (props) => props.fields.label.value || 'Untitled' }
        ),
      },
    }),
  },
});
