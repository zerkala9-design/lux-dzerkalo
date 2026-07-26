// Спільні вузли Offer для Product-схеми (Google merchant listings).
// Використовують ProductPage і LandingPage, щоб не дублювати.

// shippingRate 0 = безкоштовний самовивіз у Києві; доставка НП — окремо.
export const offerShippingDetails = {
  '@type': 'OfferShippingDetails',
  shippingRate: { '@type': 'MonetaryAmount', value: 0, currency: 'UAH' },
  shippingDestination: { '@type': 'DefinedRegion', addressCountry: 'UA' },
  deliveryTime: {
    '@type': 'ShippingDeliveryTime',
    handlingTime: { '@type': 'QuantitativeValue', minValue: 2, maxValue: 7, unitCode: 'DAY' },
    transitTime: { '@type': 'QuantitativeValue', minValue: 1, maxValue: 3, unitCode: 'DAY' },
  },
};

export const merchantReturnPolicy = {
  '@type': 'MerchantReturnPolicy',
  applicableCountry: 'UA',
  returnPolicyCategory: 'https://schema.org/MerchantReturnNotPermitted',
};
