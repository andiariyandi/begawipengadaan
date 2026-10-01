<?php if (! empty($registrationProfile)): ?>
<script>
(() => {
    const data = <?= json_encode($registrationProfile, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    Object.entries(data).forEach(([name, value]) => {
        const fields = document.querySelectorAll('[name="' + name + '"], [name="' + name + '[]"]');
        fields.forEach((field) => {
            const values = Array.isArray(value) ? value.map(String) : [String(value ?? '')];
            if (field.type === 'checkbox' || field.type === 'radio') {
                field.checked = values.includes(field.value);
            } else if (field.tagName === 'SELECT' && field.multiple) {
                Array.from(field.options).forEach((option) => { option.selected = values.includes(option.value); });
            } else if (field.tagName !== 'SELECT' || values.includes(field.value)) {
                field.value = values[0];
            }
        });
    });
})();
</script>
<?php endif; ?>
