<template>
    <div class="px-4 pt-5 sm:px-6 lg:px-8">
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <h1 class="text-base font-semibold leading-6 text-gray-900">Objetivos de venta</h1>
          <p class="mt-2 text-sm text-gray-700">Procentaje -10%</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
          <button type="button" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Exportar</button>
        </div>
      </div>
      <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <table class="min-w-full divide-y divide-gray-300">
              <thead>
                <tr>
                  <th scope="col" class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Zona</th>
                  <th scope="col" class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Cadena</th>
                  <th scope="col" class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">PDV</th>
                  <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Unidades</th>
                  <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Pesos</th>
                  <th scope="col" class="relative whitespace-nowrap py-3.5 pl-3 pr-4 sm:pr-0">
                    <span class="sr-only">Edit</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="transaction in transactions" :key="transaction.id">
                  <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">{{ transaction.zona }}</td>
                  <td class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">{{ transaction.cadena }}</td>
                  <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-900">{{ transaction.pvd }}</td>
                  <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-500">{{ transaction.unidades }}</td>
                  <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-500">{{ transaction.pesos }}</td>
                  <td class="relative whitespace-nowrap py-2 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                    <a href="#" class="text-indigo-600 hover:text-indigo-900"
                      >Edit<span class="sr-only">, {{ transaction.id }}</span></a
                    >
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  const transactions = [
    {
      id: 'AAPS0L',
      zona: 'CABA/GBA',
      cadena: 'JULERIAQUE',
      pvd: 'ABASTO',
      unidades: '78',
      pesos: '$12,213,509.00',
      company: 'Chase & Co.',
      share: 'CAC',
      commission: '+$4.37',
      price: '$3,509.00',
      quantity: '12.00',
      netAmount: '$4,397.00',
    },
    {
      id: 'AAPS0L',
      zona: 'CABA/GBA',
      cadena: 'JULERIAQUE',
      pvd: 'ARCOS',
      unidades: '79',
      pesos: '$10,213,509.00',
      company: 'Chase & Co.',
      share: 'CAC',
      commission: '+$4.37',
      price: '$3,509.00',
      quantity: '12.00',
      netAmount: '$4,397.00',
    },
    {
      id: 'AAPS0L',
      zona: 'CABA/GBA',
      cadena: 'JULERIAQUE',
      pvd: 'ALTO PALERMO',
      unidades: '90',
      pesos: '$16,213,509.00',
      company: 'Chase & Co.',
      share: 'CAC',
      commission: '+$4.37',
      price: '$3,509.00',
      quantity: '12.00',
      netAmount: '$4,397.00',
    },
    {
      id: 'AAPS0L',
      zona: 'CABA/GBA',
      cadena: 'JULERIAQUE',
      pvd: 'CABILDO',
      unidades: '77',
      pesos: '$13,213,509.00',
      company: 'Chase & Co.',
      share: 'CAC',
      commission: '+$4.37',
      price: '$3,509.00',
      quantity: '12.00',
      netAmount: '$4,397.00',
    },
    {
      id: 'AAPS0L',
      zona: 'CABA/GBA',
      cadena: 'JULERIAQUE',
      pvd: 'FLORIDA',
      unidades: '59',
      pesos: '$6,213,509.00',
      company: 'Chase & Co.',
      share: 'CAC',
      commission: '+$4.37',
      price: '$3,509.00',
      quantity: '12.00',
      netAmount: '$4,397.00',
    },
    // More transactions...
  ]
  </script>