import React from 'react'
import { render } from 'react-dom'

import AddressAutosuggest from '../components/AddressAutosuggest'

// @see https://developer.mozilla.org/fr/docs/Web/API/Element/closest#Polyfill
if (!Element.prototype.matches)
  Element.prototype.matches = Element.prototype.msMatchesSelector ||
                              Element.prototype.webkitMatchesSelector

if (!Element.prototype.closest)
  Element.prototype.closest = function(s) {
    var el = this
    if (!document.documentElement.contains(el)) return null
    do {
      if (el.matches(s)) return el
      el = el.parentElement || el.parentNode
    } while (el !== null && el.nodeType == 1)

    return null
  }

export default function(el, options) {

  const {
    existingAddressControl,
    newAddressControl,
    isNewAddressControl,
    moreOptionsContainer
  } = options

  const addresses = []
  Array.from(existingAddressControl.options).forEach(option => {
    if (option.dataset.address) {
      addresses.push(JSON.parse(option.dataset.address))
    }
  })

  // Replace the existing address dropdown by a hidden input with the same name & value
  const existingAddressControlHidden = document.createElement('input')

  const existingAddressControlName = existingAddressControl.name
  const existingAddressControlValue = existingAddressControl.value
  const existingAddressControlSelected = existingAddressControl.options[existingAddressControl.selectedIndex]

  existingAddressControlHidden.setAttribute('type', 'hidden')
  existingAddressControlHidden.setAttribute('name', existingAddressControlName)
  existingAddressControlHidden.setAttribute('value', existingAddressControlValue)

  existingAddressControl.remove()
  el.appendChild(existingAddressControlHidden)

  // Replace the new address text field by a hidden input with the same name & value
  const newAddressControlHidden = document.createElement('input')

  const newAddressControlName = newAddressControl.name
  const newAddressControlValue = newAddressControl.value

  newAddressControlHidden.setAttribute('type', 'hidden')
  newAddressControlHidden.setAttribute('name', newAddressControlName)
  newAddressControlHidden.setAttribute('value', newAddressControlValue)

  newAddressControl.remove()
  el.appendChild(newAddressControlHidden)

  // Replace the new address checkbox by a hidden input with the same name & value
  const isNewAddressControlHidden = document.createElement('input')

  const isNewAddressControlName = isNewAddressControl.name
  const isNewAddressControlValue = isNewAddressControl.value

  isNewAddressControlHidden.setAttribute('type', 'hidden')
  isNewAddressControlHidden.setAttribute('name', isNewAddressControlName)
  isNewAddressControlHidden.setAttribute('value', isNewAddressControlValue)

  isNewAddressControl.closest('.checkbox').remove()
  if (isNewAddressControl.checked) {
    el.appendChild(isNewAddressControlHidden)
  }

  // Callback with initial data
  if (existingAddressControlSelected.dataset.address) {
    options.onReady(JSON.parse(existingAddressControlSelected.dataset.address))
  }

  const reactContainer = document.createElement('div')

  el.prepend(reactContainer)

  render(
    <AddressAutosuggest
      addresses={ addresses }
      address={ existingAddressControlSelected.text }
      geohash={ '' }
      required={ true }
      reportValidity={ true }
      preciseOnly={ true }
      onAddressSelected={ (value, address) => {

        if (address.id) {
          existingAddressControlHidden.value = address['@id']
          isNewAddressControlHidden.remove()
          moreOptionsContainer.classList.add('hidden')
        } else {
          newAddressControlHidden.value = address.streetAddress
          el.querySelector('[data-address-prop="postalCode"]').value = address.postalCode
          el.querySelector('[data-address-prop="addressLocality"]').value = address.addressLocality
          el.querySelector('[data-address-prop="latitude"]').value = address.latitude
          el.querySelector('[data-address-prop="longitude"]').value = address.longitude

          moreOptionsContainer.classList.remove('hidden')

          if (!document.documentElement.contains(isNewAddressControlHidden)) {
            el.appendChild(isNewAddressControlHidden)
          }
        }

        options.onChange(address)

      } } />,
    reactContainer
  )
}
