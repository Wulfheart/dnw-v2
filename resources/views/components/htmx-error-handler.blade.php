<div>
    <style>
        #htmx-error-modal-backdrop {
            display: none; /* Hide by default */
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
        }
        #htmx-error-modal-content {
            background-color: #fefefe;
            /*margin: 50px auto; !* 200px from the top and centered *!*/
            padding: 0;
            width: 100%; /* Full width minus the margin */
            height: 100%; /* Full height minus the margin */
            position: relative;
        }
        #htmx-error-modal-content iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
    <div id="htmx-error-modal-backdrop" onclick="closeHtmxErrorModal()">
        <div id="htmx-error-modal-content" onclick="event.stopPropagation()"></div>
    </div>
    <script>
        function closeHtmxErrorModal() {
            document.getElementById('htmx-error-modal-backdrop').style.display = 'none';
            document.getElementById('htmx-error-modal-content').innerHTML = '';
        }

        });
    </script>
</div>
