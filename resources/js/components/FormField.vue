<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="fullWidthContent"
  >
    <template #field>
      <div class="w-full">
        <div v-for="(permissions, group) in field.options" :key="group" class="mb-4">
          <h1 class="font-normal text-lg mb-3 my-2">
            <Checkbox :checked="isGroupChecked(group)" @input="toggleGroup(group)" />
            <button type="button" class="ml-1" @click="toggleGroup(group)">
              {{ __(group) }}
            </button>
          </h1>
          <div class="grid grid-cols-4 gap-4 break-words">
            <div v-for="permission in permissions" :key="permission.option">
              <Checkbox
                :value="permission.option"
                :checked="isChecked(permission.option)"
                @input="toggleOption(permission.option)"
              />
              <button type="button" class="ml-1" @click="toggleOption(permission.option)">
                {{ permission.label }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';

export default {
  mixins: [
    FormField,
    HandlesValidationErrors
  ],
  props: [
    'resourceName',
    'resourceId',
    'field'
  ],
  data: () => ({}),
  methods: {
    availableOptions(group) {
      return this.field.options[group];
    },

    isChecked(option) {
      return this.value && this.value.includes(option);
    },

    isGroupChecked(group) {
      const options = this.availableOptions(group);
      return options.length > 0 && options.every(permission => this.isChecked(permission.option));
    },

    check(option) {
      if (!this.isChecked(option)) {
        this.value = [...(this.value || []), option];
      }
    },

    uncheck(option) {
      if (this.isChecked(option)) {
        this.value = this.value.filter(item => item !== option);
      }
    },

    toggleGroup(group) {
      const checked = this.isGroupChecked(group);

      this.availableOptions(group).forEach(
        (permission) => checked
          ? this.uncheck(permission.option)
          : this.check(permission.option)
      )
    },

    toggleOption(option) {
      this.isChecked(option) ? this.uncheck(option) : this.check(option);
    },

    setInitialValue() {
      this.value = this.field.value || [];
    },

    fill(formData) {
      this.fillIfVisible(formData, this.fieldAttribute, JSON.stringify(this.value || []));
    },

    handleChange(value) {
      this.value = value;
    }
  }
};
</script>
