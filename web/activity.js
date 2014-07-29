new Vue({el: '#activities',
    methods: {
        submit: function(e) {
            $form = $('#activity-form');
            var $data = this.$data;

            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: $form.serialize(),
            }).fail(function (xhr){
                $data['error'] = xhr.getResponseHeader('X-Error-Message');
            }).done(function (data){
                $('textarea', $form).val('');

                if ($data['activities'].length == 0) {
                    location.reload();
                    return;
                }

                $data['activities'].unshift(data);

                if ($data['activities'].length > $data.size) {
                    $data['activities'].pop();
                }
            });

            e.preventDefault();
        },
        postStamp: function(e, stamp) {
            $form = $('#activity-form');
            $('input[name="stamp"]', $form).val(stamp);
            this.submit(e);
        },
        formatStamp: function(stamp) {
            return '<img width=150 height=150 src=/stamp/'+stamp+'.jpg alt='+stamp+' />';
        },
        afterPage: function(direction) {
            var $data = this.$data;
            return parseInt($data.page) + parseInt(direction)
        },
        paging: function(e, direction) {
            var $data = this.$data;
            var url = $(e.target).attr('href');

            $.ajax({url: url}).done(function (data){
                window.history.pushState(null, 'Home', url);
                for (k in data) {
                    $data[k] = data[k];
                }
            });

            e.preventDefault();
        },
    },
    data: data,
});
