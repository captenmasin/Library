<script setup lang="ts">
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { PropType } from 'vue';
import { Book } from '@/types/book';
import { useForm } from '@inertiajs/vue3';
import { useRoute } from '@/composables/useRoute';

const props = defineProps({
    book: Object as PropType<Book>,
})

const noteForm = useForm({
    content: props.book.notes?.content || '',
});

const submit = () => {
    noteForm.post(useRoute('books.notes.store', props.book), {
        preserveScroll: true
    });
};

</script>

<template>
    <div>
        {{ noteForm }}
        <form @submit.prevent="submit">
            <Textarea v-model="noteForm.content" placeholder="Add notes about this book..." />
            <Button> Save </Button>
            {{ noteForm.errors }}
        </form>
    </div>
</template>
