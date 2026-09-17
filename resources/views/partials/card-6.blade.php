<style>
    @keyframes issueLabel {
        0% {
            opacity: 0;
            letter-spacing: 8px;
        }

        100% {
            opacity: 1;
            letter-spacing: 3px;
        }
    }

    @keyframes issueTitle {
        0% {
            opacity: 0;
            transform: translateX(25px);
        }

        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes issueAlert {
        0% {
            opacity: 0;
            transform: scale(.4);
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
        }

        55% {
            opacity: 1;
            transform: scale(1.15);
            box-shadow: 0 0 0 6px rgba(220, 38, 38, .12);
        }

        100% {
            opacity: 1;
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
        }
    }

    @keyframes issueLine {
        0% {
            opacity: 0;
            transform: scaleX(0);
            transform-origin: center;
        }

        100% {
            opacity: 1;
            transform: scaleX(1);
            transform-origin: center;
        }
    }

    @keyframes issueBody {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    .issue-label {
        animation: issueLabel .45s ease-out both;
    }

    .issue-title {
        animation: issueTitle .5s cubic-bezier(.16, 1, .3, 1) .2s both;
    }

    .issue-alert {
        animation: issueAlert .55s cubic-bezier(.16, 1, .3, 1) .3s both;
    }

    .issue-line {
        animation: issueLine .65s cubic-bezier(.16, 1, .3, 1) .55s both;
    }

    .issue-body {
        animation: issueBody .35s ease-out .9s both;
    }
</style>

<div class="py-2">

    <div class="issue-label"
        style="font-size: 10px; letter-spacing: 3px; color: #ea580c; font-weight: 600;">
        FORMAT ACTIVITY PLAN
    </div>

    <div class="d-flex align-items-center justify-content-between mt-1">

        <div class="issue-title"
            style="font-size: 29px; line-height: 1; font-weight: 700; color: #431407;">
            Issue & Komplain
        </div>

        <div class="issue-alert"
            style="
                width: 27px;
                height: 27px;
                border: 1.5px solid #ea580c;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #ea580c;
                font-size: 16px;
                font-weight: 600;
            ">
            !
        </div>

    </div>

    <div class="mt-3 issue-line"
        style="height: 2px; background: linear-gradient(to right, #ea580c 52%, #ea580c 52%, #e2e8f0 43%, #e2e8f0 100%);">
    </div>

    <div class="d-flex align-items-center justify-content-between mt-3 issue-body">
        
    </div>

</div>