import json
import re


def string_match_kmp(text, query):
    """
    String matching using KMP algorithm
    :param text:
    :type text:
    :param query:
    :type query:
    :return:
    :rtype:
    """

    def init_KMP_table(query):
        """
        Create KMP support table
        :param query:
        :type query:
        :return:
        :rtype:
        """

        # initialize list
        fail = [0 for _ in range(0, len(query))]
        fail[0] = 0

        j = 0
        i = 1

        while i < len(query):

            # if pattern at j and i match, continue looking for longest sequence match
            if query[i] == query[j]:
                fail[i] = j + 1
                j += 1
                i += 1

            # find closest 'check point' to start with
            elif j > 0:
                j = fail[j - 1]

            # j still at 0, using i to find first letter that match j
            else:
                fail[i] = 0
                i += 1

        return fail

    result_table = []
    support_table = init_KMP_table(query)

    i = 0  # iterator text
    j = 0  # iterator query

    while i < len(text):

        if query[j] == text[i]:
            if j == len(query) - 1:

                found = (i + 1 - len(query), i + 1)
                result_table.append(found)  # return found (idx_start, idx_end)

                # continue searching, search for other occurrence
                i += 1
                j = 0
                continue

            i += 1
            j += 1

        elif j > 0:
            j = support_table[j - 1]

        else:
            i += 1

    return result_table


def string_match_bm(text, query):
    """
    String matching with Boyer Moore algorithm
    :param text:
    :type text:
    :param query:
    :type query:
    :return:
    :rtype:
    """

    def init_BM_table(query):
        """
        initialize Boyer moore support table
        :param query:
        :type query:
        :return:
        :rtype:
        """

        last_occurence = [-1 for _ in range(0, 128)]

        for i in range(0, len(query)):
            last_occurence[ord(query[i])] = i

        return last_occurence

    result_table = []

    len_query = len(query)
    len_text = len(text)

    # query is longer than text
    if len_query > len_text:
        # return -1 # return not found in index
        return result_table

    support_table = init_BM_table(query)

    i = len_query - 1  # iterator text
    j = len_query - 1  # iterator query

    while i < len_text:

        if query[j] == text[i]:

            # if query is same until the first letter,
            if j == 0:

                result_table.append((i, i + len(query)))

                # continue searching, search for other occurrence
                i += 1
                j = 0
                continue

            # not at first letter yet, continue checking
            else:
                i -= 1
                j -= 1

        # letter different, jump to next checkpoint
        else:

            # set text to correct checkpoint
            jump_offset = support_table[ord(text[i])]
            i += len_query - min(j, 1 + jump_offset)

            # restart checking at the last letter of query
            j = len_query - 1

    return result_table


def string_match_regex(text, query):

    result_table = []
    results = re.finditer(query, text, flags=re.IGNORECASE)
    for result in results:
        result_table.append(result.span())

    return result_table


def check_text(text, query, method):

    if "kmp" in method:
        return string_match_kmp(text, query)

    elif "bm" in method:
        return string_match_bm(text, query)

    elif "re" in method:
        return string_match_regex(text, query)


def parse_json(filename):

    json_data = json.load(open(filename, 'r'))
    statuses = json_data['statuses']

    return statuses


def check_is_spam(statuses, spam_keywords, method):

    # check for each status
    for status in statuses:

        text = status['text']

        status['spam_occurrence'] = []

        for query in spam_keywords:

            spam_occurrence = check_text(text, query, method)
            if len(spam_occurrence) > 0:
                status['spam_occurrence'] += spam_occurrence

    return statuses


if __name__ == '__main__':

    # sample of query
    spams = ["[M]arni", "Papua"]

    filename = "sample.json"

    statuses = parse_json(filename)

    scanned_statuses = check_is_spam(statuses, spam_keywords=spams, method="re")

    # print(scanned_statuses)
    # for status in scanned_statuses:
    #     occurrences = status['spam_occurrence']
    #     for occur in occurrences:
    #         print(occur)
    #         start_idx, end_idx = occur
    #         print(status['text'][start_idx:end_idx])

    text = "the rain in spain staysd mainly rain rain on the plain"
    query = "rain"

    result = string_match_kmp(text, query)
    print(result)

    result = string_match_bm(text, query)
    print(result)

    result = string_match_regex(text, query)
    print(result)