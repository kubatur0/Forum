<script>

    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        props: ['thread'],
        components: {Replies, SubscribeButton},
        data() {
            return {
                repliesCount: this.thread.replies_count,
                locked: this.thread.locked,
                editing: false,
                form: {
                    title: this.thread.title,
                    body: this.thread.body
                }
            }
        },
        methods: {
            toggle() {
                axios[this.locked ? 'delete' : 'post']('/locked-threads/' + this.thread.slug);

                this.locked = !this.locked;

            },
            cancel() {
                this.form = {
                    title: this.thread.title,
                    body: this.thread.body
                };

                this.editing = false;
            },
            update() {
                axios.patch('/threads/' + this.thread.channel.slug + '/' + this.thread.slug, this.form).then(() => {
                    flash('You have edited a thread');
                    this.editing = false;
                });
            }
        }

    }
</script>