@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm']) }}
    onfocus="this.setAttribute('data-placeholder', this.getAttribute('placeholder')); this.setAttribute('placeholder', '');"
    onblur="if(this.value===''){ this.setAttribute('placeholder', this.getAttribute('data-placeholder')); }">
