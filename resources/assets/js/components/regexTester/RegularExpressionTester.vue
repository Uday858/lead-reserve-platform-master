<template>
    <div class="panel panel-default">
        <div class="panel-heading clean">
            Regular Express Pattern Testing
        </div>
        <div class="panel-body">
            <form>
                <div class="form-group">
                    <label>Response from Advertiser</label>
                    <textarea class="form-control" placeholder="Response Contents" name="response_contents"
                              v-model="form.contents"></textarea>
                </div>
                <div class="form-group">
                    <label>Pattern Matcher</label>
                    <textarea class="form-control" placeholder="Regular Expression" name="regex_pattern"
                              v-model="form.pattern"></textarea>
                </div>
                <div class="form-group">
                    <label>Selection</label>
                    <input type="text" class="form-control" placeholder="Selection Match" v-model="form.selection"/>
                    <p>
                        <small>
                            If the first selector is 0, only 1 number needs to be present.<br/>
                            If the first selector is not 0, two numbers need to be present, in comma-separated format.
                        </small>
                    </p>
                    <p>
                        <small>
                            [1][1] &mdash; 1,1<br/>
                            [0][2] &mdash; 2
                        </small>
                    </p>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-success" v-on:click="reloadElementFrame(form)">Test</button>
                </div>
            </form>
            <div id="regex-debug-iframe-holder"></div>
        </div>
    </div>
</template>
<script type="text/babel">
    export default {
        name: "RegularExpressionTester",
        data: function () {
            return {
                form: {
                    contents: "",
                    pattern: "",
                    selection: ""
                },
                regexDebuggerURL: ""
            }
        },
        methods:{
            reloadElementFrame: function(formObject) {
                var regexDebuggerURL = "/dashboard/developer-tools/regex-debug?contents=" + encodeURI(formObject.contents) + "&pattern=" + btoa(formObject.pattern) + "&selection=" + encodeURI(formObject.selection);
                $("#regex-debug-iframe-holder").html("").append('<iframe src="'+regexDebuggerURL+'" style="border:none;width:250px;height:250px;"></iframe>');
            }
        }
    }
</script>