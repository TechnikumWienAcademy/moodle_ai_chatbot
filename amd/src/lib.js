var questionString = 'Ask a question...'
var errorString = 'An error occurred! Please try again later.'

export const init = (data) => {

    console.log("start")

    //console.log(data)

    const modId = data['modId']
    //const blockId = data['blockId']
    //const blockId = modId
    const api_type = data['api_type']
    const persistConvo = data['persistConvo']
    //const canAskQuestions = data['canAskQuestions']
    const userId = data['userId']

    // Initialize local data storage if necessary
    // If a thread ID exists for this block, make an API request to get existing messages
    if (api_type === 'assistant') {

        chatData = localStorage.getItem("mod_openaichat_data")
        console.log(chatData)
        //console.log(chatData)
        if (chatData) {
            chatData = JSON.parse(chatData)
            //console.log(chatData[modId]['threadId'])

            if (chatData[modId] && chatData[modId]['threadId'] && persistConvo === "1") {
                console.log('here')
                fetch(`${M.cfg.wwwroot}/mod/openaichat/api/thread.php?modId=${modId}&thread_id=${chatData[modId]['threadId']}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    for (let message of data) {
                        addToChatLog(message.role === 'user' ? 'user' : 'bot', message.message)
                    }
                })
                // Some sort of error in the API call. Probably the thread no longer exists, so lets reset it
                .catch(error => {
                    chatData[modId] = {}
                    localStorage.setItem("mod_openaichat_data", JSON.stringify(chatData));
                })
            // The block ID doesn't exist in the chat data object, so let's create it
            } else {
                chatData[modId] = {}
            }
        // We don't even have a chat data object, so we'll create one
        } else {
            chatData = {[modId]: {}}
        }
        localStorage.setItem("mod_openaichat_data", JSON.stringify(chatData));
    }

    if (checkUserLimit(modId, userId) == false){
        return disableButton()
    }

    //remaining number of questions.
    if (checkUserLimit(modId, userId) !== -1) {
        console.log(checkUserLimit(modId, userId))
        document.querySelector("#remaining-questions").innerText = "You have " + checkUserLimit(modId, userId) + " question(s) remaining.";
    }

    document.querySelector('#openai_input').addEventListener('keyup', e => {

        if (e.which === 13 && e.target.value !== "") {
            if (checkUserLimit(modId, userId) == false){
                return disableButton()
            }
            addToChatLog('user', e.target.value)
            createCompletion(e.target.value, modId, api_type, userId)
            e.target.value = ''
        }
    })
    document.querySelector('.mod_openaichat #go').addEventListener('click', e => {

        const input = document.querySelector('#openai_input')
        if (input.value !== "") {
            if (checkUserLimit(modId, userId) == false){
                return disableButton()
            }
            addToChatLog('user', input.value)
            createCompletion(input.value, modId, api_type, userId)
            input.value = ''
        }
    })

    document.querySelector('.mod_openaichat #refresh').addEventListener('click', e => {
        clearHistory(modId)
    })

    require(['core/str'], function(str) {
        var strings = [
            {
                key: 'askaquestion',
                component: 'mod_openaichat_data'
            },
            {
                key: 'erroroccurred',
                component: 'mod_openaichat_data'
            },
        ];
        str.get_strings(strings).then((results) => {
            questionString = results[0];
            errorString = results[1];
        });
    });
}

/**
 * Add a message to the chat UI
 * @param {string} type Which side of the UI the message should be on. Can be "user" or "bot"
 * @param {string} message The text of the message to add
 */
const addToChatLog = (type, message) => {

    let messageContainer = document.querySelector('#openai_chat_log')

    const messageElem = document.createElement('div')
    messageElem.classList.add('openai_message')
    for (let className of type.split(' ')) {
        messageElem.classList.add(className)
    }

    const messageText = document.createElement('span')
    messageText.innerHTML = message
    messageElem.append(messageText)

    messageContainer.append(messageElem)
    if (messageText.offsetWidth) {
        messageElem.style.width = (messageText.offsetWidth + 40) + "px"
    }
    messageContainer.scrollTop = messageContainer.scrollHeight
}

/**
 * Clears the thread ID from local storage and removes the messages from the UI in order to refresh the chat
 */
const clearHistory = (modId) => {
    chatData = localStorage.getItem("mod_openaichat_data")
    if (chatData) {
        chatData = JSON.parse(chatData)
        if (chatData[modId]) {
            chatData[modId] = {}
            localStorage.setItem("mod_openaichat_data", JSON.stringify(chatData));
        }
    }
    document.querySelector('#openai_chat_log').innerHTML = ""
}

/**
 * Makes an API request to get a completion from GPT-3, and adds it to the chat log
 * @param {string} message The text to get a completion for
 * @param {int} modId The ID of the block this message is being sent from -- used to override settings if necessary
 * @param {string} api_type "assistant" | "chat" The type of API to use
 */
const createCompletion = (message, modId, api_type, userId) => {

    let threadId = null
    let chatData

    if (checkUserLimit(modId, userId) == false){
        return disableButton()
    }

    // If the type is assistant, attempt to fetch a thread ID
    if (api_type === 'assistant') {
        chatData = localStorage.getItem("mod_openaichat_data")
        if (chatData) {
            chatData = JSON.parse(chatData)
            if (chatData[modId]) {
                threadId = chatData[modId]['threadId'] || null
            }
        } else {
            // create the chat data item if necessary
            chatData = {[modId]: {}}
        }
    }

    const history = buildTranscript()

    document.querySelector('.mod_openaichat #control_bar').classList.add('disabled')
    document.querySelector('#openai_input').classList.remove('error')
    document.querySelector('#openai_input').placeholder = questionString
    document.querySelector('#openai_input').blur()
    addToChatLog('bot loading', '...');

    fetch(`${M.cfg.wwwroot}/mod/openaichat/api/completion.php`, {
        method: 'POST',
        body: JSON.stringify({
            message: message,
            history: history,
            modId: modId,
            threadId: threadId
        })
    })
    .then(response => {
        let messageContainer = document.querySelector('#openai_chat_log')
        messageContainer.removeChild(messageContainer.lastElementChild)
        document.querySelector('.mod_openaichat #control_bar').classList.remove('disabled')

        if (!response.ok) {
            throw Error(response.statusText)
        } else {
            return response.json()
        }
    })
    .then(data => {
        try {
            console.log(data)
            storeUserLog(modId, message, data.message)
            addToChatLog('bot', data.message)
            if (data.thread_id) {
                chatData[modId]['threadId'] = data.thread_id
                localStorage.setItem("mod_openaichat_data", JSON.stringify(chatData));
            }
        } catch (error) {
            console.log(error)
            addToChatLog('bot', data.error.message)
        }
        setTimeout(function(){
            if (checkUserLimit(modId, userId) == false){
                return disableButton()
            }else{
                if (checkUserLimit(modId, userId) !== -1) {
                    document.querySelector("#remaining-questions").innerText = "You have " + checkUserLimit(modId, userId) + " question(s) remaining.";
                }
                document.querySelector('#openai_input').focus()
            }
        }, 50)
    })
    .catch(error => {
        console.log(error)
        document.querySelector('#openai_input').classList.add('error')
        document.querySelector('#openai_input').placeholder = errorString
    })
}

/**
 * Using the existing messages in the chat history, create a string that can be used to aid completion
 * @return {JSONObject} A transcript of the conversation up to this point
 */
const buildTranscript = () => {
    let transcript = []
    document.querySelectorAll('.openai_message').forEach((message, index) => {
        if (index === document.querySelectorAll('.openai_message').length - 1) {
            return
        }

        let user = userName
        if (message.classList.contains('bot')) {
            user = assistantName
        }
        transcript.push({"user": user, "message": message.innerText})
    })

    return transcript
}

//ajax call to store chat log and user log to DB.
const storeUserLog = (modId, requestMessage, responseMessage) => {
    $.ajax({
        method: "POST",
        url: `${M.cfg.wwwroot}/mod/openaichat/api/record_log.php`,
        data: {"modId" : modId, "requestMessage" : requestMessage, "responseMessage" : responseMessage},
        dataType: "text"
    })
}

//ajax call to see if the user can still ask questions.
const checkUserLimit = (modId, userId) => {
    return $.ajax({
        method: "POST",
        url: `${M.cfg.wwwroot}/mod/openaichat/api/question_counter.php`,
        data: {"modId" : modId, "userId" : userId},
        dataType: "JSON",
        async: false,
        success: function(result){
            a = result
        }
    }).responseJSON
}

//disabling and giving style to button if there is no limit left.
function disableButton(){
    document.querySelector('.mod_openaichat #control_bar').classList.add('disabled')
    document.querySelector('#openai_input').classList.add('error')
    document.querySelector('#openai_input').placeholder = "Limit reached"
    document.querySelector('#remaining-questions').style.display = "none"
    return false
}